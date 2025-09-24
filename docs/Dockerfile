FROM python:3.11-slim

# Set working directory
WORKDIR /docs

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements
COPY requirements.txt .

# Install Python dependencies
RUN pip install --no-cache-dir -r requirements.txt

# Copy documentation files
COPY . .

# Expose port
EXPOSE 8000

# Default command
CMD ["mkdocs", "serve", "--dev-addr=0.0.0.0:8000"]
