FROM golang:1.25-bookworm AS build
WORKDIR /src
COPY go.mod ./
COPY main.go ./
RUN CGO_ENABLED=0 go build -o /mock-backend main.go

FROM gcr.io/distroless/static-debian12
COPY --from=build /mock-backend /mock-backend
EXPOSE 8080
ENTRYPOINT ["/mock-backend"]
