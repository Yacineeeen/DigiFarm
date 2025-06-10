from flask import Flask, request, jsonify
import pandas as pd
import joblib

app = Flask(__name__)

# Load the trained model
model = joblib.load('maintenance_model.pkl')

@app.route('/predict', methods=['POST'])
def predict():
    # Get JSON data from the request
    data = request.get_json(force=True)
    
    # Create a DataFrame with the correct feature names
    df = pd.DataFrame([{
        'cout': data['cout'],
        'temperature': data['temperature'],
        'humidite': data['humidite'],
        'consoCarburant': data['consoCarburant'],
        'consoEnergie': data['consoEnergie']
    }])
    
    # Make prediction
    prediction = model.predict(df)
    
    # Return the prediction as JSON
    return jsonify({'prediction': prediction[0]})

if __name__ == '__main__':
    app.run(debug=True)