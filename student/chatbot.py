import logging
from flask import Flask, request, jsonify
import mysql.connector
from transformers import GPT2Tokenizer, GPTNeoForCausalLM
from rapidfuzz import process
from textblob import Word
import re
from flask_cors import CORS

# Initialize Flask app
app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Setup logging
logging.basicConfig(level=logging.DEBUG, format='%(asctime)s - %(levelname)s - %(message)s')
logger = logging.getLogger(__name__)

# Load pre-trained GPT-Neo 125M model and tokenizer from Hugging Face
logger.info("Loading GPT-Neo 125M model and tokenizer")
tokenizer = GPT2Tokenizer.from_pretrained('EleutherAI/gpt-neo-125M')
model = GPTNeoForCausalLM.from_pretrained('EleutherAI/gpt-neo-125M')
tokenizer.pad_token = tokenizer.eos_token

# Database connection function
def connect_to_db():
    try:
        logger.info("Connecting to the database")
        return mysql.connector.connect(
            host="localhost",
            user="root",
            password="",
            database="lms"
        )
    except mysql.connector.Error as e:
        logger.error(f"Error connecting to the database: {e}")
        raise

# Function to correct spelling errors
def correct_spelling(user_input):
    logger.info(f"Correcting spelling for input: {user_input}")
    corrected_words = [Word(word).correct() for word in user_input.split()]
    return " ".join(corrected_words)

# Query possibilities for categorization
query_possibilities = {
    "course": ["courses", "class", "program", "subject", "lesson", "curriculum"],
    "announcement": ["announcement", "notice", "update", "news", "bulletin"],
    "department": ["department", "faculty", "unit", "school"],
    "topic": ["topic", "chapter", "section", "module"],
    "assignment": ["assignment list", "task", "homework", "project"],
    "lecturer": ["lecturer", "teacher", "professor", "instructor"]
}

# Find best match for input using RapidFuzz
def find_best_match(user_input):
    logger.info(f"Finding best match for input: {user_input}")
    all_possibilities = [(category, term) for category, terms in query_possibilities.items() for term in terms]
    match = process.extractOne(user_input.lower(), [term for _, term in all_possibilities], score_cutoff=30)
    if match:
        logger.debug(f"Best match: {match[0]} (Score: {match[1]})")
        return next((cat for cat, term in all_possibilities if term == match[0]), None)
    logger.debug("No match found")
    return None

# Extract IDs from user input
def extract_ids(user_input):
    logger.info(f"Extracting course_id and topic_id from input: {user_input}")
    course_id = re.search(r"course\s*(\d+)", user_input)
    topic_id = re.search(r"topic\s*(\d+)", user_input)
    return int(course_id.group(1)) if course_id else None, int(topic_id.group(1)) if topic_id else None

# Fetch data from database
def fetch_db_data(category, db_connection, extra_data=None):
    queries = {
        "course": "SELECT course_id, course_name, course_description FROM courses WHERE is_active = 1",
        "announcement": """
            SELECT announcement_id, announcement, created_date 
            FROM course_announcements
            WHERE is_active = 1 AND (%s IS NULL OR course_id = %s)
        """,
        "department": "SELECT department_name, description FROM departments WHERE is_active = 1",
        "topic": "SELECT topic_name, course_id FROM topics WHERE is_active = 1",
        "assignment": """
            SELECT DISTINCT a.assignment_name, a.assignment_description, a.due_date, t.topic_name, c.course_name
            FROM assignments a
            JOIN topics t ON a.topic_id = t.topic_id
            JOIN courses c ON t.course_id = c.course_id
            WHERE a.is_active = 1 AND (%s IS NULL OR t.course_id = %s)
        """,
        "lecturer": "SELECT first_name, last_name, department FROM lecturers WHERE is_active = 1"
    }

    query = queries.get(category)
    if not query:
        logger.warning(f"No query available for category: {category}")
        return []

    cursor = db_connection.cursor(dictionary=True)
    if extra_data:
        cursor.execute(query, extra_data)
    else:
        cursor.execute(query)
    results = cursor.fetchall()
    logger.debug(f"Fetched results for {category}: {results}")
    return results

# Template for generating responses for various categories
def response_templates(db_results, category):
    logger.info(f"Generating response template for category: {category}")
    
    if not db_results:
        return "I couldn't find any relevant information. Could you clarify your request?"

    # Make response more conversational and readable
    response = "Here’s what I found:\n"
    
    for result in db_results:
        if category == "course":
            response += f"\nThere's a course titled '{result['course_name']}' with course ID {result['course_id']}. It is focused on {result['course_description']}.\n"
        elif category == "announcement":
            response += f"\nAn announcement was made: '{result['announcement']}'. It was created on {result['created_date']}.\n"
        elif category == "department":
            response += f"\nThe department '{result['department_name']}' offers courses that specialize in {result['description']}.\n"
        elif category == "topic":
            response += f"\nThere's a topic called '{result['topic_name']}' which is part of the course with course ID {result['course_id']}.\n"
        elif category == "assignment":
            response += f"\nAn assignment named '{result['assignment_name']}' is due on {result['due_date']}. It is related to the topic '{result['topic_name']}' from the course '{result['course_name']}'. Here's a brief description: {result['assignment_description']}.\n"
        elif category == "lecturer":
            response += f"\nLecturer {result['first_name']} {result['last_name']} is part of the '{result['department']}' department.\n"
    
    return response

# Generate a response using GPT-Neo
def generate_response(user_input, db_results, category):
    logger.info(f"Generating response for input: {user_input}")
    return response_templates(db_results, category)

@app.route("/chat", methods=["POST"])
def chat():
    logger.info("Received a chat request")
    user_input = request.json.get("message", "").strip()

    if not user_input:
        logger.warning("No message provided in the request")
        return jsonify({"response": "Please send a valid message."})

    # Convert input to lowercase for easier matching
    user_input_lower = user_input.lower()

    # Check for greetings
    greetings = ['hi', 'hello', 'hey', 'hola', 'good morning', 'good afternoon', 'good evening']
    if any(greeting in user_input_lower for greeting in greetings):
        response = "Hi, I'm ApexBot, an AI-built chatbot specially designed for Apex Institute, first time in Sri Lanka! 😊 How can I assist you today?"
    elif any(phrase in user_input_lower for phrase in ["how to submit assignment", "how to submit assignments", "submit my assignment", "how do i submit assignment" , "how to submit assigments"]):
      response = (
        "Here’s how you can submit your assignment:\n"
        "- Step 1 : Go to the course section in the sidebar and click it.\n"
            '<img src="./images/AssigmentSteps/AssigmentStep1.png" alt="Assignment Submission Guide" style="width: 200px; height: auto;" />\n'
        "- step 2 : Click View Course on the course card. \n"
                    '<img src="./images/AssigmentSteps/AssigmentStep2.png" alt="Assignment Submission Guide" style="width: 200px; height: auto;" />\n'
        "- Step 3 : Now click 'Assignments' under the topics section.\n"
                            '<img src="./images/AssigmentSteps/AssigmentStep4.png" alt="Assignment Submission Guide" style="width: 500px; height: 200px;" />\n'
        "- Step 4 : Finally, upload your submission file in the card..\n"
                            '<img src="./images/AssigmentSteps/AssigmentStep3.png" alt="Assignment Submission Guide" style="width: 300px; height: auto;" />\n'
      )
    # Check for 'What can you do' questions
    elif "what can you do" in user_input_lower or "how can you help" in user_input_lower:
        response = (
            "I'm ApexBot, and I'm here to assist you with a variety of things! 😊 Here's how I can help:\n"
            "- I can help you find details about courses and their descriptions.\n"
            "- I can show announcements and updates related to your courses.\n"
            "- Need information on assignments? I can help with that too!\n"
            "- I can provide details on departments, lecturers, and topics.\n"
            "- You can ask me about course materials, homework, and more.\n\n"
            "Just ask me anything, and I'll do my best to assist you! 😄"
        )
        
        # Check for 'What can you do' questions
    elif "my assigment is very large" in user_input_lower or "My assignment is very large. I can't upload it." in user_input_lower:
        response = (
        "Oh... that means your assignment is quite large! 📄✨ That’s great—you've put in a lot of effort! 😊\n\n"
        "No worries! If your assignment is too large to upload, you can upload it to a cloud storage service (like Google Drive) and generate a shareable link. 🔗📂\n\n"
        "Then, add that link to a Word document and upload the document instead. That way, everything will be accessible! 👍🚀"
    )        

    # Check for 'Who are you?' or similar questions
    elif "who are you" in user_input_lower or "what are you" in user_input_lower or "tell me about yourself" in user_input_lower or "who are u" in user_input_lower:
        response = (
            "I'm ApexBot, a specially designed AI chatbot for Apex Institute to assist my lovely students! 😊"
            "Right now, I'm still learning and under development by my creators."
            "I was developed by Amectar , a software development and AI generative company founded by Aman Manthira.\n"
            "Feel free to ask me anything, and I'll do my best to help you! 🚀"
        )

    else:
        # Correct the spelling and process further for non-greeting messages
        user_input = correct_spelling(user_input)
        db_connection = connect_to_db()

        try:
            category = find_best_match(user_input)
            if not category:
                logger.warning("No category matched the user input")
                response = "I'm sorry, I couldn't understand your request. Could you clarify?"
            else:
                course_id, topic_id = extract_ids(user_input)
                extra_data = (course_id, topic_id) if category in ["announcement", "assignment"] else None
                db_results = fetch_db_data(category, db_connection, extra_data)
                response = generate_response(user_input, db_results, category)
        except Exception as e:
            logger.error(f"An error occurred: {e}")
            response = f"An error occurred: {e}"
        finally:
            db_connection.close()

    logger.info(f"Returning response: {response}")
    return jsonify({"response": response})

# Run the Flask app
if __name__ == "__main__":
    app.run(debug=True)
