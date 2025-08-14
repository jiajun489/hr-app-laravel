#!/bin/bash

# Install dependencies
pip install -r requirements.txt

# Set environment variables
export API_KEY=hr-analytics-secret-key

# Start the Flask application
python app.py