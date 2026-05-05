# Security Notes

This repository is a security research portal and should be treated carefully when used with real application queries.

## Built-In Controls

- CSRF token validation on the detector form.
- Security headers for framing, MIME sniffing, referrer policy, and content security policy.
- PDO prepared statements for database operations.
- Environment-based configuration for database credentials.
- Input length validation for submitted statements.
- Statement hashing for detection records.

## Production Recommendations

- Add authentication and role-based authorization.
- Enforce HTTPS.
- Restrict database access to the application network.
- Redact sensitive SQL before persistence.
- Log security events to centralized monitoring.
- Treat detector output as decision support, not as the only control.

## Secure Coding Reminder

SQL injection detection is not a substitute for secure development practices. Applications should still use prepared statements, least-privilege database accounts, input validation, output encoding, and secure error handling.
