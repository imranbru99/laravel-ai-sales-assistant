# REST API Documentation (v1)

AI Sales Assistant provides a versioned REST API (`/api/v1`) authenticated via Laravel Sanctum Bearer tokens.

Base URL: `http://your-domain.com/api/v1`

---

## Authentication

Generate an API token inside Tenant Settings or programmatically via Sanctum:
```http
Authorization: Bearer <sanctum_api_token>
Accept: application/json
Content-Type: application/json
```

---

## Endpoints

### 1. System Health
```http
GET /api/v1/health
```
**Response:**
```json
{
  "status": "healthy",
  "version": "1.0.0",
  "timestamp": "2026-11-04T05:28:00Z"
}
```

---

### 2. User Context
```http
GET /api/v1/user
```
Returns authenticated user profile, tenant ID, and assigned roles.

---

### 3. Leads Management & AI Scoring

#### List Leads
```http
GET /api/v1/leads?status=qualified&grade=A&per_page=15
```

#### Create & Auto-Score Lead
```http
POST /api/v1/leads
```
**Payload:**
```json
{
  "contact_name": "Sarah Connor",
  "company_name": "Cyberdyne Systems",
  "email": "sarah@cyberdyne.io",
  "source": "linkedin",
  "budget": 25000,
  "status": "new"
}
```
**Response (201 Created):**
```json
{
  "success": true,
  "message": "Lead created and scored successfully.",
  "data": {
    "id": 105,
    "contact_name": "Sarah Connor",
    "company_name": "Cyberdyne Systems",
    "score": 88,
    "score_grade": "A",
    "score_explanation": {
      "score": 88,
      "grade": "A",
      "explanation": "High-priority enterprise prospect. Verified corporate domain, robust budget exceeding $25k...",
      "recommended_action": "Send personalized value proposal highlighting ROI and schedule a discovery call."
    }
  }
}
```

#### Re-Score Lead on Demand
```http
POST /api/v1/leads/{id}/score
```
Recalculates AI score, records snapshot in `lead_scores` table, and returns updated score and recommended action.

---

### 4. Deals & Pipeline

#### List Deals
```http
GET /api/v1/deals?pipeline_id=1&stage_id=2
```

#### Create Deal
```http
POST /api/v1/deals
```
**Payload:**
```json
{
  "title": "Supply Contract: Cyberdyne Phase 2",
  "pipeline_id": 1,
  "stage_id": 2,
  "lead_id": 105,
  "value": 25000,
  "currency": "USD"
}
```

---

### 5. Multichannel Follow-up Assistant

#### Generate AI Draft
```http
POST /api/v1/follow-ups/draft
```
**Payload:**
```json
{
  "lead_id": 105,
  "channel": "email",
  "tone": "consultative",
  "language": "en",
  "save": true
}
```
**Response:**
```json
{
  "success": true,
  "data": {
    "draft": {
      "subject": "Tailored follow-up regarding Cyberdyne Systems expansion",
      "content": "Dear Sarah Connor,\n\nFollowing our review of your requirements..."
    },
    "follow_up": {
      "id": 42,
      "status": "draft",
      "channel": "email"
    }
  }
}
```
