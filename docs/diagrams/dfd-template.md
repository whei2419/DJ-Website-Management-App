# Data Flow Diagram — DJ & Voter Web App (Mermaid)

```mermaid
flowchart LR
  %% === External Entities ===
  DJ["DJ (registrant)\n(name, email, phone, profile pic, slot)"]
  Voter["Voter (enters phone number to vote)"]
  Admin[Admin]
  DigitalJourney["Digital Journey — External API\nfetches name & email by phone"]

  %% === Processes / Services ===
  WebApp["Web Application (Frontend)"]
  API["API Server / Backend"]

  %% === Data Stores / Storage ===
  DB[("Primary Database\n(DJ profiles, votes, logs)")]

  %% === Flows ===
  DJ -->|registers: name, email, phone, profile pic, slot| WebApp
  WebApp -->|POST /register| API
  API -->|store image file on server and save link in DB| DB
  API -->|create DJ record| DB
  API -->|log event| DB

  Admin -->|admin UI| WebApp
  WebApp -->|admin calls| API
  API -->|read/write DJs| DB

  Voter -->|enter phone, select DJ to vote| WebApp
  WebApp -->|POST /vote: phone dj_id| API
  API -->|query Digital Journey by phone| DigitalJourney
  DigitalJourney -->|returns name email confirm identity| API
  API -->|check prior vote for phone & dj_id| DB
  API -->|if allowed: write vote record| DB
  API -->|increment vote count| DB
  
  API -->|log vote event| DB

  %% Add or refine nodes and arrows below as needed.
```

---