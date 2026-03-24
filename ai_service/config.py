import os
from dotenv import load_dotenv

load_dotenv()

# Database — same as Laravel .env
DB_HOST = os.getenv('DB_HOST', '127.0.0.1')
DB_PORT = int(os.getenv('DB_PORT', 3306))
DB_NAME = os.getenv('DB_DATABASE', 'bloodbridge_db')
DB_USER = os.getenv('DB_USERNAME', 'root')
DB_PASSWORD = os.getenv('DB_PASSWORD', 'password')

# Model paths
MODEL_PATH = os.path.join(os.path.dirname(
    __file__), 'models', 'donor_scorer.pkl')
FEATURES_PATH = os.path.join(os.path.dirname(
    __file__), 'models', 'feature_names.pkl')

# Scoring
NEUTRAL_SCORE = 0.5
MIN_HISTORY_FOR_MODEL = 1
