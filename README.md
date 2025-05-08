# Log Analytics API

A Symfony 7 application for analyzing and querying log files.

## Installation Using Docker

1. Clone the repository:
```bash
git clone https://github.com/AdamZdeschitsInnowise/log-analytics.git
```

2. Copy `.env.example` to `.env` in both the docker and app root directories.
```bash
make copy-env
```

3. Start the containers:
```bash
make start
```

4. Install dependencies:
```bash
make install
```

## Usage

### Importing Logs

To send a message for importing logs from a file:

```bash
make import
```

To consume messages and import logs:

```bash
make consume
```

### API Endpoint

The application provides a `/count` endpoint that accepts the following query parameters:

- `serviceNames[]`: Array of service names to filter by
- `statusCode`: HTTP status code to filter by
- `startDate`: Start date in ISO 8601 format
- `endDate`: End date in ISO 8601 format

Example request:
```
GET /count?serviceNames[]=USER-SERVICE&statusCode=201&startDate=2018-08-17T09:21:53Z&endDate=2018-08-17T09:23:55Z
```

Example response:
```json
{
    "counter": 5
}
```

## Development

### Running Tests

```bash
make test
```