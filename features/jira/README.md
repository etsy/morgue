JIRA Feature
---

### Overview

- We want to know the status of Jira tickets

### Testing

For testing https://jira.atlassian.com can be used.

```
    {   "name": "jira",
        "enabled": "on",
        "baseurl": "https://jira.atlassian.com",
        "username": "",
        "password": "",
        "proxy": "http://myproxy:8080",
        "additional_fields" : {
        }
    },
```

### Troubleshooting

If the HTTP response is not 200 OK a message like this will be sent to the `error_log`:

    Got unexpected HTTP status code 401 from https://jira.atlassian.com/rest/api/2/search
