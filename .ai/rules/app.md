---
paths:
  - 'app/**'
---

# App

## Use repositories for CRM domain persistence
For the CRM leads/calls API, keep persistence behind repository contracts and Eloquent implementations. Controllers should depend on actions or repository contracts, while business status transitions stay in CreateCallAction.
