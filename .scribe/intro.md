# Introduction

Comprehensive, production-ready RESTful API for OmniCore Mobile Apps (Flutter, React Native, iOS, Android) and Web Clients.

<aside>
    <strong>Base URL</strong>: <code>http://one_dashboard.test</code>
</aside>

    Welcome to the **OmniCore API Reference**.
    
    All API requests must be made over HTTPS. Responses are returned in standard JSON format containing `status`, `message`, and `data` objects.
    
    ### Authentication
    Protected endpoints require a **Bearer Token** in the HTTP `Authorization` header:
    ```http
    Authorization: Bearer <your_access_token>
    ```
    Obtain your token by invoking `POST /api/login` or `POST /api/register`.

