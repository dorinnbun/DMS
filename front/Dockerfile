# Use official Node image with Alpine for smaller size
FROM node:20-alpine

# Set working directory
WORKDIR /var/www

# Copy package files first to leverage Docker layer caching
COPY package*.json ./

# Remove any existing modules or lockfiles (if copied accidentally)
RUN rm -rf node_modules package-lock.json || true && \
    npm cache clean --force

# Install all dependencies including vite
RUN npm install

# Copy the rest of the app source code
COPY . .

# Expose port that Vite uses
EXPOSE 3000

# Start the dev server
CMD ["npm", "run", "dev"]
