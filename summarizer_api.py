from flask import Flask, request, jsonify
from flask_cors import CORS
from transformers import T5Tokenizer, T5ForConditionalGeneration
import re

app = Flask(__name__)
CORS(app)  


# Load model
model_name = "t5-small"
tokenizer = T5Tokenizer.from_pretrained(model_name)
model = T5ForConditionalGeneration.from_pretrained(model_name)

def capitalize_sentences(text):
    sentences = re.split(r'(\. |\! |\? )', text)
    sentences = [sentences[i] + (sentences[i+1] if i+1 < len(sentences) else '')
                 for i in range(0, len(sentences), 2)]
    return ''.join([s[0].upper() + s[1:] if s and s[0].islower() else s for s in sentences])

@app.route("/summarize", methods=["POST"])
def summarize():
    data = request.get_json()
    input_text = data.get("text", "")

    if not input_text.strip():
        return jsonify({"summary": ""})

    inputs = tokenizer.encode("summarize: " + input_text, return_tensors="pt", max_length=1024, truncation=True)
    summary_ids = model.generate(inputs, max_length=150, min_length=50, length_penalty=2.0, num_beams=4, early_stopping=True)
    summary = tokenizer.decode(summary_ids[0], skip_special_tokens=True)
    summary = capitalize_sentences(summary)
    return jsonify({"summary": summary})

if __name__ == "__main__":
    app.run(debug=True)
