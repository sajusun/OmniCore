# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_BEARER_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Retrieve your access token by calling the <code>POST /api/login</code> or <code>POST /api/register</code> endpoint.
