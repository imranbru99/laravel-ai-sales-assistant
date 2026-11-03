# Entity Relationship Diagram (ERD)

This document visualizes the core database schemas and relationships across the platform using Mermaid diagrams.

---

## 1. Tenancy, Organization & Access Control

```mermaid
erDiagram
    TENANTS ||--o{ BRANCHES : "has many"
    TENANTS ||--o{ DEPARTMENTS : "has many"
    TENANTS ||--o{ USERS : "employs"
    TENANTS ||--o{ TENANT_MODULES : "activates"
    BRANCHES ||--o{ USERS : "assigned to"
    BRANCHES ||--o{ BRANCH_MODULES : "overrides"
    MODULES ||--o{ TENANT_MODULES : "belongs to"

    TENANTS {
        bigint id PK
        string name
        string slug UK
        string plan
        string status
        string currency
        string business_type
        json settings
    }

    BRANCHES {
        bigint id PK
        bigint tenant_id FK
        string name
        string code
        boolean is_headquarters
        boolean is_active
    }

    USERS {
        bigint id PK
        bigint tenant_id FK
        bigint branch_id FK
        string name
        string email UK
        string password
        boolean is_super_admin
        boolean is_active
    }

    MODULES {
        bigint id PK
        string key UK
        string name
        string category
        json dependencies
        boolean is_core
        decimal price_monthly
    }
```

---

## 2. AI Sales Core & CRM Pipeline

```mermaid
erDiagram
    TENANTS ||--o{ LEADS : "owns"
    TENANTS ||--o{ DEALS : "owns"
    USERS ||--o{ LEADS : "manages"
    USERS ||--o{ DEALS : "owns"
    LEADS ||--o{ LEAD_SCORES : "history"
    LEADS ||--o{ FOLLOW_UPS : "scheduled"
    PIPELINES ||--o{ PIPELINE_STAGES : "contains"
    PIPELINE_STAGES ||--o{ DEALS : "current stage"
    DEALS ||--o{ FOLLOW_UPS : "follow-up sequence"

    LEADS {
        bigint id PK
        bigint tenant_id FK
        bigint branch_id FK
        string contact_name
        string company_name
        string email
        string status
        decimal budget
        int score
        char score_grade
        json score_explanation
        text recommended_next_action
    }

    LEAD_SCORES {
        bigint id PK
        bigint tenant_id FK
        bigint lead_id FK
        int score
        char score_grade
        text explanation
        text recommended_action
        string event_trigger
    }

    DEALS {
        bigint id PK
        bigint tenant_id FK
        bigint pipeline_id FK
        bigint stage_id FK
        bigint lead_id FK
        string title
        decimal value
        string currency
        int probability
        string status
    }

    FOLLOW_UPS {
        bigint id PK
        bigint tenant_id FK
        bigint lead_id FK
        bigint deal_id FK
        string channel
        string status
        string subject
        text content
        string tone
        datetime scheduled_at
    }
```

---

## 3. Commerce, Invoicing & Field People

```mermaid
erDiagram
    TENANTS ||--o{ QUOTATIONS : "issues"
    TENANTS ||--o{ INVOICES : "bills"
    INVOICES ||--o{ PAYMENTS : "collects"
    TENANTS ||--o{ EMPLOYEES : "employs"
    EMPLOYEES ||--o{ ATTENDANCE_LOGS : "punches"
    ATTENDANCE_DEVICES ||--o{ ATTENDANCE_LOGS : "captures"

    QUOTATIONS {
        bigint id PK
        bigint tenant_id FK
        string number UK
        string recipient_name
        decimal total
        string status
        string language
    }

    INVOICES {
        bigint id PK
        bigint tenant_id FK
        string number UK
        string client_name
        decimal total
        decimal paid_amount
        string status
    }

    PAYMENTS {
        bigint id PK
        bigint tenant_id FK
        bigint invoice_id FK
        decimal amount
        string gateway
        string transaction_id
        string status
    }

    EMPLOYEES {
        bigint id PK
        bigint tenant_id FK
        bigint user_id FK
        string employee_code UK
        string designation
        decimal base_salary
    }

    ATTENDANCE_LOGS {
        bigint id PK
        bigint tenant_id FK
        bigint device_id FK
        bigint employee_id FK
        datetime punch_time
        string punch_state
        string punch_hash
    }
```
