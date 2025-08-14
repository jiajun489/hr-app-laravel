# HR Analytics Python Microservice

## Setup

1. Install Python dependencies:
```bash
pip install -r requirements.txt
```

2. Start the service:
```bash
./start.sh
```

Or manually:
```bash
export API_KEY=hr-analytics-secret-key
python app.py
```

## API Endpoints

### POST /analyze
Analyzes HR data and returns insights.

**Headers:**
- `X-API-KEY`: hr-analytics-secret-key
- `Content-Type`: application/json

**Request Body:**
```json
{
  "users": [{"id": 1, "name": "John Doe", "department": "IT"}],
  "tasks": [{"id": 1, "assigned_to": 1, "status": "pending", "due_date": "2025-01-01"}],
  "presences": [{"employee_id": 1, "status": "present", "date": "2025-01-01"}]
}
```

**Response:**
```json
{
  "workload": [{"employee_name": "John Doe", "active_tasks": 5}],
  "bottlenecks": {"pending": 10, "in_progress": 5, "completed": 25},
  "productivity": [{"employee_name": "John Doe", "lateness_rate": 5.0, "completion_rate": 95.0}],
  "overdue": [{"employee_name": "John Doe", "overdue_count": 3}]
}
```

### GET /health
Health check endpoint.

## Running in Production

Use Gunicorn:
```bash
gunicorn -w 4 -b 0.0.0.0:5000 app:app
```