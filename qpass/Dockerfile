FROM golang:1.25-bookworm AS build
WORKDIR /src
COPY go.mod ./
COPY main.go ./
RUN CGO_ENABLED=0 go build -o /qpass main.go

FROM gcr.io/distroless/static-debian12
COPY --from=build /qpass /qpass
COPY certs /certs
EXPOSE 8443
ENTRYPOINT ["/qpass"]
