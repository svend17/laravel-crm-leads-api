# CRM Leads API

This project is a small Laravel API for managing CRM leads and their calls.
It exposes endpoints to create leads, add calls to a lead, and list paginated leads assigned to a manager.
Managers are created through factories or seeders because the assignment does not require a public manager API.

## Business logic

The main business logic is implemented in `App\Actions\CreateCallAction`.
That action creates a call inside a database transaction, locks the lead row, assigns the first manager when needed, and updates lead status according to the required call rules.
Lead statuses cannot be changed directly through the API; they are derived from call creation only.

## Persistence and responses

Persistence is accessed through repository contracts and Eloquent implementations to keep controllers and actions decoupled from query details.
The manager lead list uses Laravel API Resources, pagination, `withCount`, and `withSum` to avoid loading all calls.

## What can be improved

- Add authorization when the API needs user-level access control.
- Store `manager_id` on calls if audit history should show which manager made each call.
- Add OpenAPI documentation for external API consumers.
- Dispatch domain events after a call is created, so future analytics, notifications, or integrations can be handled outside the action.
- Move post-call side effects to queued jobs, while keeping call creation and lead status transitions synchronous inside the transaction.
- Add caching or denormalized read models for manager lead aggregates if `withCount` and `withSum` become too expensive on larger datasets.
- Add filtering and sorting to the manager leads endpoint as the CRM list grows.
- Add database indexes for common lookup and aggregation paths, especially manager lead lists and latest lead calls.
- Introduce a small internal base Eloquent repository only once several repositories repeat the same CRUD, pagination, or sorting code. At the current size, a generic repository abstraction or package would mostly wrap one-line Eloquent calls while the important methods are domain-specific, such as locked lead lookup, manager lead aggregates, and latest call results.
