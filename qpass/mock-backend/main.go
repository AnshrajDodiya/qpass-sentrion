package main

import (
	"encoding/json"
	"log"
	"net/http"
)

func main() {
	http.HandleFunc("/", func(w http.ResponseWriter, r *http.Request) {
		w.Header().Set("Content-Type", "application/json")
		json.NewEncoder(w).Encode(map[string]string{
			"service": "mock-legacy-banking",
			"path":    r.URL.Path,
			"status":  "ok",
		})
	})
	log.Println("mock legacy backend listening on :8080")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
