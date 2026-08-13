# CRM Leads API

This project is a small Laravel API for managing CRM leads and their calls.
It exposes endpoints to create leads, add calls to a lead, and list paginated leads assigned to a manager.
Managers are created through factories or seeders because the assignment does not require a public manager API.
The main business logic is implemented in `App\Actions\CreateCallAction`.
That action creates a call inside a database transaction, locks the lead row, assigns the first manager when needed, and updates lead status according to the required call rules.
Persistence is accessed through repository contracts and Eloquent implementations to keep controllers and actions decoupled from query details.
Lead statuses cannot be changed directly through the API; they are derived from call creation only.
The manager lead list uses Laravel API Resources, pagination, `withCount`, and `withSum` to avoid loading all calls.
Possible improvements include authorization, storing `manager_id` on calls for audit history, OpenAPI documentation, and domain events for analytics.
