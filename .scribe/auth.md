# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer Bearer {YOUR_API_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

To authenticate, first call the <b>login</b> or <b>register</b> endpoint to get a token. Then include it in the Authorization header as: <code>Bearer {token}</code>
