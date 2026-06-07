import os
import xgboost as xgb
import pandas as pd
from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

# Load Model
MODEL_PATH = 'xgboost_numerasi.json'
if os.path.exists(MODEL_PATH):
    model = xgb.XGBClassifier()
    model.load_model(MODEL_PATH)
    print(f"Model {MODEL_PATH} berhasil dimuat.")
else:
    model = None
    print(f"WARNING: Model {MODEL_PATH} tidak ditemukan. Harap jalankan script training terlebih dahulu.")

# 36 fitur One-Hot Encoding yang sama persis dengan format saat training
FEATURES = [
    'Q1_1_A', 'Q1_1_B', 'Q1_1_C',
    'Q2_2_A', 'Q2_2_B', 'Q2_2_C',
    'Q3_3_A', 'Q3_3_B', 'Q3_3_C',
    'Q4_4_A', 'Q4_4_B', 'Q4_4_C',
    'Q5_5_A', 'Q5_5_B', 'Q5_5_C',
    'Q6_6_A', 'Q6_6_B', 'Q6_6_C',
    'Q7_7_A', 'Q7_7_B', 'Q7_7_C',
    'Q8_8_A', 'Q8_8_B', 'Q8_8_C',
    'Q9_9_A', 'Q9_9_B', 'Q9_9_C',
    'Q10_10_A', 'Q10_10_B', 'Q10_10_C',
    'Q11_11_A', 'Q11_11_B', 'Q11_11_C',
    'Q12_12_A', 'Q12_12_B', 'Q12_12_C'
]

# Pemetaan integer kelas ke Label String (berdasarkan urutan abjad scikit-learn LabelEncoder)
CLASS_MAPPING = {
    0: 'Advanced',
    1: 'Basic',
    2: 'NSI',
    3: 'Proficient'
}

@app.route('/predict', methods=['POST'])
def predict():
    if model is None:
        return jsonify({'error': 'Model ML belum dilatih atau file tidak ditemukan.'}), 500
        
    try:
        req = request.get_json()
        if not req or 'answers' not in req:
            return jsonify({'error': 'Payload harus berisi key "answers"'}), 400
            
        answers = req.get('answers', []) # Harap format array string seperti ['1_A', '2_C', ...]
        
        if len(answers) != 12:
            return jsonify({'error': f'Jumlah jawaban harus 12, tapi menerima {len(answers)}.'}), 400
            
        # Bentuk data DataFrame satu baris berisi 0 semua untuk One-Hot Encoding
        input_data = {feat: [False] for feat in FEATURES}
        
        # Tandai True untuk setiap jawaban yang dipilih oleh siswa
        for i, ans in enumerate(answers):
            q_num = i + 1
            feature_name = f"Q{q_num}_{ans}"
            if feature_name in input_data:
                input_data[feature_name][0] = True
                
        df_input = pd.DataFrame(input_data)
        
        # Eksekusi XGBoost Prediction Probabilities
        proba = model.predict_proba(df_input)[0]
        
        # Ambil kelas dengan persentase tertinggi
        pred_idx = int(proba.argmax())
        predicted_level = CLASS_MAPPING[pred_idx]
        
        # Susun persentase per kelas agar bisa dibaca manusia
        probabilities = {
            CLASS_MAPPING[i]: round(float(p) * 100, 1) for i, p in enumerate(proba)
        }
        
        return jsonify({
            'status': 'success',
            'level': predicted_level,
            'probabilities': probabilities
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/health', methods=['GET'])
def health():
    return jsonify({'status': 'running', 'model_loaded': model is not None})

if __name__ == '__main__':
    # Jalankan server API di port 5000
    app.run(host='0.0.0.0', port=5000, debug=False)
