# Use a lightweight Node.js image based on Alpine Linux
FROM node:20

# Set the working directory
WORKDIR /var/www

# Copy both package.json and package-lock.json (if available)
COPY package*.json ./

# Remove node_modules and package-lock.json if they exist (to clean up)
RUN rm -rf node_modules package-lock.json
RUN rm -rf node_modules
RUN npm cache clean --force


RUN npm install vuetify

# Install dependencies
RUN npm install

# Copy the rest of the application code
COPY . .

# Set the environment variable
# ENV NODE_ENV=production

# Run any build scripts if necessary (e.g., if using a frontend build tool)
RUN npm run build

# Expose the appropriate port if your app runs on a specific port
EXPOSE 3000

# Command to run your application (adjust based on your app's entry point)
CMD ["npm","run","dev"]
