``` mermaid
  flowchart TD
    A[Browser] -->|HTTP Request| B[Routes/web.php]
    B --> C[Controller]
    C --> D[Model]
    D --> E[Database]
    C --> F[View]
    F -->|HTML Response| A

```