import pandas as pd
from xgboost import XGBClassifier
from sklearn.preprocessing import LabelEncoder
import warnings
warnings.filterwarnings('ignore')

# 1. Load Data
df = pd.read_csv('numeracy_dataset.csv')
X = pd.get_dummies(df.drop(['student_id', 'Target_Level'], axis=1))
y = df['Target_Level']

# 2. Encode Target
le = LabelEncoder()
y_encoded = le.fit_transform(y)

# 3. Train Model (Sama seperti di notebook)
model = XGBClassifier(random_state=42, use_label_encoder=False, eval_metric='mlogloss')
model.fit(X, y_encoded)

# 4. Extract Feature Importances
importances = model.feature_importances_
feature_names = X.columns

feature_importance_df = pd.DataFrame({
    'Opsi Jawaban (Fitur)': feature_names,
    'Tingkat Kepentingan (Importance)': importances
})

# Sort by highest importance
feature_importance_df = feature_importance_df.sort_values(by='Tingkat Kepentingan (Importance)', ascending=False)

print("=== TOP 10 OPSI JAWABAN PALING MENENTUKAN (FEATURE IMPORTANCE) ===")
print("Ini membuktikan apakah model sadar ada soal yang lebih 'berbobot/sulit' daripada soal lain:")
print("-" * 65)
print(feature_importance_df.head(15).to_string(index=False))
