import json
from datetime import datetime
import textwrap

import ast
import torch
from transformers import BertTokenizer, BertForSequenceClassification

import json

import warnings
warnings.filterwarnings("ignore")

json_file1 = 'output.json'

# Path to the JSON file
json_file = 'submitted_code.json'


# Load pre-trained BERT model and tokenizer for error classification (This part is just an outline for NLP-based error feedback)
model = BertForSequenceClassification.from_pretrained('bert-base-uncased')
tokenizer = BertTokenizer.from_pretrained('bert-base-uncased')

def classify_error_with_bert(error_message):
    # Tokenize the error message and classify using BERT (just a simple classification example)
    inputs = tokenizer(error_message, return_tensors="pt", truncation=True, padding=True, max_length=512)
    outputs = model(**inputs)
    logits = outputs.logits
    predicted_class = torch.argmax(logits, dim=1).item()
    
    # Simple mockup: In practice, you'd have more specific classifications for various errors
    if predicted_class == 0:
        return "Syntax-related error, likely due to incorrect indentation or missing symbols."
    else:
        return "Other types of error, could be related to undefined variables or logic issues."

def check_python_code_errors(code_snippet):
    try:
        # Parse the code snippet
        parsed_code = ast.parse(code_snippet)
        # Try to compile the parsed code
        compile(parsed_code, filename="<string>", mode="exec")
        print("No syntax errors found!")
    except SyntaxError as e:
        # Use BERT-based classification for enhanced error description
        detailed_error1 = classify_error_with_bert(str(e))
        print(f"Syntax Error: {e}\nDetailed feedback: {detailed_error1}")
        return detailed_error1

    except Exception as e:
        # Use BERT-based classification for enhanced error description
        detailed_error2 = classify_error_with_bert(str(e))
        print(f"Other Error: {e}\nDetailed feedback: {detailed_error2}")

        return detailed_error2
        



from bs4 import BeautifulSoup
from transformers import RobertaTokenizer, RobertaForSequenceClassification
import torch

# Load the RobertaTokenizer and model for CodeBERT
tokenizer = RobertaTokenizer.from_pretrained("microsoft/codebert-base")
model = RobertaForSequenceClassification.from_pretrained("microsoft/codebert-base")

def check_html_errors_with_codebert(html_code):
    try:
        # Parse the HTML code with BeautifulSoup
        soup = BeautifulSoup(html_code, "html.parser")
        
        # Collect warnings or issues
        errors = []
        
        # Check for unclosed tags
        unclosed_tags = [tag.name for tag in soup.find_all() if not tag.find_all(recursive=False)]
        if unclosed_tags:
            errors.append(f"Unclosed tags found: {', '.join(unclosed_tags)}")
        
        # Check for missing DOCTYPE
        if not html_code.strip().lower().startswith("<!doctype html>"):
            errors.append("DOCTYPE declaration is missing or incorrect.")
        
        # Use CodeBERT to analyze the HTML code for potential issues
        input_ids = tokenizer.encode(html_code, return_tensors="pt", max_length=512, truncation=True)
        
        # Get the prediction (whether the code is valid or has issues)
        with torch.no_grad():
            outputs = model(input_ids)
            logits = outputs.logits
            predicted_class = torch.argmax(logits, dim=1).item()
        
        # If predicted_class is 1, the model identifies a potential issue with the HTML code
        if predicted_class == 1:
            errors.append("Potential issues detected in the HTML code (CodeBERT analysis).")
        
        # Return errors if any, else indicate valid HTML
        if errors:
            return "\n".join(errors)
        else:
            return "HTML code is valid!"
    
    except Exception as e:
        return f"An error occurred: {str(e)}"






try:
    # Read the JSON file
    with open(json_file, 'r') as file:
        data = json.load(file)

    # Check if the JSON data is a non-empty list
    if isinstance(data, list) and data:
        # Sort the entries by timestamp (most recent first)
        sorted_data = sorted(data, key=lambda x: datetime.strptime(x['timestamp'], '%Y-%m-%d %H:%M:%S'), reverse=True)

        # Get the most recent entry
        most_recent_entry = sorted_data[0]

        # Display the most recent code
        cdd=most_recent_entry['code']
        lng=most_recent_entry['language']

        if lng=="Python":
            print("python")
            python_code = textwrap.fill(cdd, width=50)
            err=check_python_code_errors(python_code)
  
            print(type(err))
            data1 = {"message": str(err)}

            # Open the JSON file and write the string to it
            with open(json_file1, 'w') as file:
                json.dump(data1, file, indent=4)
                
        if lng=="HTML":
            print("html")
            html_code = textwrap.fill(cdd, width=50)
            errors = check_html_errors_with_codebert(html_code)
            print(errors)
            data2 = {"message": errors}

            # Open the JSON file and write the string to it
            with open(json_file1, 'w') as file:
                json.dump(data2, file, indent=4)

        
    else:
        print("No data found in the JSON file.")

except FileNotFoundError:
    print("JSON file not found.")
except json.JSONDecodeError:
    print("Error decoding JSON file.")
except KeyError:
    print("Missing expected keys in JSON data.")
