# Use a lightweight Node.js image based on Alpine Linux
FROM node:18-alpine

# Set the working directory
WORKDIR /var/www

# Copy only the package.json and package-lock.json first for optimized caching
COPY package.json package-lock.json ./

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Set the environment variable
ENV NODE_ENV=production

# Run any build scripts if necessary (e.g., if using a frontend build tool)
RUN npm run build

# Expose the appropriate port if your app runs on a specific port
EXPOSE 3000

# Command to run your application (adjust based on your app's entry point)
CMD ["npm","run", "dev"]
