package main

import (
	"bytes"
	"crypto/tls"
	"encoding/json"
	"log"
	"net"
	"net/http"
	"net/http/httputil"
	"net/url"
	"os"
	"strconv"
	"time"
)

// QPassLog matches the "What Q-PASS Logs" fields from the architecture diagram.
type QPassLog struct {
	Timestamp    string `json:"timestamp"`
	UserID       string `json:"user_id,omitempty"`
	SourceIP     string `json:"source_ip"`
	Endpoint     string `json:"endpoint"`
	Method       string `json:"http_method"`
	StatusCode   int    `json:"status_code"`
	SessionID    string `json:"session_id,omitempty"`
	TLSHandshake string `json:"tls_handshake"`
	TLSGroup     string `json:"tls_group"`
	Encryption   string `json:"encryption_algorithm"`
}

// SentrionEvent maps to sentrion's real sensor/ event ingestion fields.
// Required: ipAddress, url, eventTime. See sensor/src/Factory/RequestFactory.php.
type SentrionEvent struct {
	IPAddress  string
	URL        string
	EventTime  string
	UserName   string
	HTTPMethod string
	HTTPCode   string
	UserAgent  string
	EventType  string
}

var (
	backendURL  *url.URL
	sentrionURL string
	sentrionKey string
	httpClient  = &http.Client{Timeout: 5 * time.Second}
)

func main() {
	backendRaw := getenv("BACKEND_URL", "http://legacy-backend:80")
	sentrionURL = getenv("SENTRION_EVENT_URL", "http://app:80/sensor/")
	sentrionKey = getenv("SENTRION_API_KEY", "")
	certFile := getenv("TLS_CERT", "certs/server.crt")
	keyFile := getenv("TLS_KEY", "certs/server.key")
	listenAddr := getenv("LISTEN_ADDR", ":8443")

	var err error
	backendURL, err = url.Parse(backendRaw)
	if err != nil {
		log.Fatalf("invalid BACKEND_URL: %v", err)
	}

	proxy := httputil.NewSingleHostReverseProxy(backendURL)

	handler := func(w http.ResponseWriter, r *http.Request) {
		rec := &statusRecorder{ResponseWriter: w, status: 200}
		proxy.ServeHTTP(rec, r)
		go logAndForward(r, rec.status)
	}

	tlsConfig := &tls.Config{
		MinVersion: tls.VersionTLS13,
		// Leaving CurvePreferences empty also gets X25519MLKEM768 by default
		// on Go 1.24+, but we set it explicitly to document intent and
		// guarantee the fallback order (classical X25519 for older clients).
		CurvePreferences: []tls.CurveID{tls.X25519MLKEM768, tls.X25519},
	}

	server := &http.Server{
		Addr:      listenAddr,
		Handler:   http.HandlerFunc(handler),
		TLSConfig: tlsConfig,
	}

	log.Printf("Q-PASS listening on %s -> backend %s", listenAddr, backendURL)
	log.Fatal(server.ListenAndServeTLS(certFile, keyFile))
}

type statusRecorder struct {
	http.ResponseWriter
	status int
}

func (r *statusRecorder) WriteHeader(code int) {
	r.status = code
	r.ResponseWriter.WriteHeader(code)
}

func logAndForward(r *http.Request, status int) {
	group := "none"
	handshake := "none"
	if r.TLS != nil {
		group = r.TLS.CurveID.String()
		handshake = tls.VersionName(r.TLS.Version)
	}

	entry := QPassLog{
		Timestamp:    time.Now().UTC().Format(time.RFC3339),
		UserID:       r.Header.Get("X-User-Id"),
		SourceIP:     r.RemoteAddr,
		Endpoint:     r.URL.Path,
		Method:       r.Method,
		StatusCode:   status,
		SessionID:    r.Header.Get("X-Session-Id"),
		TLSHandshake: handshake,
		TLSGroup:     group,
		Encryption:   "ML-KEM-768 (hybrid X25519)",
	}

	b, _ := json.Marshal(entry)
	log.Println(string(b))

	event := SentrionEvent{
		IPAddress:  clientIP(entry.SourceIP),
		URL:        entry.Endpoint,
		EventTime:  time.Now().UTC().Format("2006-01-02 15:04:05"),
		UserName:   entry.UserID,
		HTTPMethod: entry.Method,
		HTTPCode:   itoa(status),
		UserAgent:  r.UserAgent(),
		EventType:  "gateway_request",
	}
	forwardToSentrion(event)
}

// clientIP strips the port from RemoteAddr (host:port) since sensor's
// IpAddress validator expects a bare IP.
func clientIP(remoteAddr string) string {
	if host, _, err := net.SplitHostPort(remoteAddr); err == nil {
		return host
	}
	return remoteAddr
}

func forwardToSentrion(event SentrionEvent) {
	form := url.Values{}
	form.Set("ipAddress", event.IPAddress)
	form.Set("url", event.URL)
	form.Set("eventTime", event.EventTime)
	if event.UserName != "" {
		form.Set("userName", event.UserName)
	}
	if event.HTTPMethod != "" {
		form.Set("httpMethod", event.HTTPMethod)
	}
	if event.HTTPCode != "" {
		form.Set("httpCode", event.HTTPCode)
	}
	if event.UserAgent != "" {
		form.Set("userAgent", event.UserAgent)
	}
	if event.EventType != "" {
		form.Set("eventType", event.EventType)
	}

	req, err := http.NewRequest(http.MethodPost, sentrionURL, bytes.NewBufferString(form.Encode()))
	if err != nil {
		log.Printf("sentrion request build error: %v", err)
		return
	}
	req.Header.Set("Content-Type", "application/x-www-form-urlencoded")
	if sentrionKey != "" {
		// sentrion's sensor endpoint authenticates via the Api-Key header,
		// not Authorization: Bearer.
		req.Header.Set("Api-Key", sentrionKey)
	}
	resp, err := httpClient.Do(req)
	if err != nil {
		log.Printf("sentrion forward error: %v", err)
		return
	}
	defer resp.Body.Close()
	if resp.StatusCode >= 300 {
		log.Printf("sentrion forward non-2xx status: %d", resp.StatusCode)
	}
}

func getenv(key, fallback string) string {
	if v := os.Getenv(key); v != "" {
		return v
	}
	return fallback
}

func itoa(i int) string {
	return strconv.Itoa(i)
}
