---
paths:
  - 'app/Actions/**'
---

# Actions

## Use CreatesCalls contract for call creation
Call creation is exposed through App\Contracts\Actions\CreatesCalls. Controllers should type-hint this contract, while App\Actions\CreateCallAction remains the implementation that owns call creation orchestration and lead status transitions.
