``` mermaid 
graph TD
    A[Project Root] --> B[src]
    A --> C[tests]
    A --> D[config]
    A --> E[docker]
    A --> F[README.md]
    A --> G[package.json]
    
    B --> B1[app.js]
    B --> B2[controllers]
    B --> B3[models]
    B --> B4[services]
    
    C --> C1[user.test.js]
    C --> C2[auth.test.js]
    
    D --> D1[database.js]
    D --> D2[env.js]
    
    E --> E1[docker-compose.yml]
    E --> E2[Dockerfile]
    
    B2 --> B21[userController.js]
    B2 --> B22[authController.js]
    
    B3 --> B31[userModel.js]
    B3 --> B32[orderModel.js]
    
    B4 --> B41[userService.js]
    B4 --> B42[emailService.js]

```