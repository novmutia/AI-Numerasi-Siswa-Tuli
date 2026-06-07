import pandas as pd
from xgboost import XGBClassifier
from sklearn.preprocessing import LabelEncoder
import numpy as np
import warnings
warnings.filterwarnings('ignore')

# 1. Siapkan struktur kolom (dummy columns) agar persis seperti saat training
df = pd.read_csv('../numeracy_dataset.csv')
X_dummy = pd.get_dummies(df.drop(['student_id', 'Target_Level'], axis=1))
kolom_fitur = X_dummy.columns

# 2. Siapkan LabelEncoder untuk menerjemahkan angka kembali ke teks (Advanced, Basic, dll)
le = LabelEncoder()
le.fit(df['Target_Level'])

# 3. Load model XGBoost yang sudah ditraining
model = XGBClassifier()
model.load_model('../xgboost_numerasi.json')

# 4. Definisikan Skenario Jawaban (12 Opsi yang dipilih)
skenario = {
    "Skenario A (Harusnya Advanced)": ["1_C","2_A","3_B","4_A","5_B","6_B","7_A","8_B","9_B","10_A","11_B","12_B"],
    "Skenario B (Harusnya Proficient)": ["1_B","2_A","3_B","4_A","5_B","6_B","7_A","8_B","9_A","10_A","11_C","12_A"],
    "Skenario C (Pasti NSI)": ["1_A","2_C","3_C","4_B","5_A","6_A","7_B","8_B","9_C","10_B","11_B","12_B"]
}

print("="*60)
print("PENGUJIAN MANUAL SKENARIO SISWA KE DALAM XGBOOST")
print("="*60)

for nama_skenario, jawaban in skenario.items():
    # Buat dataframe 1 baris untuk siswa ini
    df_siswa = pd.DataFrame([jawaban], columns=[f'Q{i+1}' for i in range(12)])
    
    # Lakukan One-Hot Encoding
    df_encoded = pd.get_dummies(df_siswa)
    
    # Samakan kolom dengan kolom training (isi 0 jika fitur tidak ada)
    df_final = df_encoded.reindex(columns=kolom_fitur, fill_value=0)
    
    # Lakukan Prediksi
    probabilitas = model.predict_proba(df_final)[0]
    prediksi_angka = model.predict(df_final)[0]
    prediksi_teks = le.inverse_transform([prediksi_angka])[0]
    
    print(f"\n[{nama_skenario}]")
    print(f"Jawaban: {jawaban}")
    print(f"Hasil Prediksi AI: >> {prediksi_teks.upper()} <<")
    
    # Tampilkan persentase probabilitas
    for i, kelas in enumerate(le.classes_):
        print(f"  - Peluang {kelas}: {probabilitas[i]*100:.2f}%")
