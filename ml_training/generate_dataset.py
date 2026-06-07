import csv
import random

# Definisi 12 Soal dan Indikator Opsinya (Sesuai QuestionSeeder)
questions = [
    # Q1
    [
        {'id': '1_A', 'indicator': 'lemah', 'text': 'tidak_memahami_operasi'},
        {'id': '1_B', 'indicator': 'parsial', 'text': 'salah_hitung_minor'},
        {'id': '1_C', 'indicator': 'tepat', 'text': 'operasi_dasar_benar'}
    ],
    # Q2
    [
        {'id': '2_A', 'indicator': 'tepat', 'text': 'identifikasi_info_tepat'},
        {'id': '2_B', 'indicator': 'lemah', 'text': 'hanya_baca_sebagian'},
        {'id': '2_C', 'indicator': 'lemah', 'text': 'salah_hitung_semua'}
    ],
    # Q3
    [
        {'id': '3_A', 'indicator': 'lemah', 'text': 'tidak_pahami_konteks'},
        {'id': '3_B', 'indicator': 'tepat', 'text': 'konteks_sederhana_benar'},
        {'id': '3_C', 'indicator': 'lemah', 'text': 'salah_hitung_konteks'}
    ],
    # Q4
    [
        {'id': '4_A', 'indicator': 'tepat', 'text': 'operasi_konteks_benar'},
        {'id': '4_B', 'indicator': 'lemah', 'text': 'salah_operasi_tambah'},
        {'id': '4_C', 'indicator': 'parsial', 'text': 'salah_hitung_minor'}
    ],
    # Q5
    [
        {'id': '5_A', 'indicator': 'parsial', 'text': 'salah_hitung_minor'},
        {'id': '5_B', 'indicator': 'tepat', 'text': 'operasi_rutin_benar'},
        {'id': '5_C', 'indicator': 'parsial', 'text': 'salah_hitung_lebih'}
    ],
    # Q6
    [
        {'id': '6_A', 'indicator': 'lemah', 'text': 'salah_konteks_harga'},
        {'id': '6_B', 'indicator': 'tepat', 'text': 'konteks_sehari_benar'},
        {'id': '6_C', 'indicator': 'lemah', 'text': 'salah_hitung_konteks'}
    ],
    # Q7
    [
        {'id': '7_A', 'indicator': 'tepat', 'text': 'konsep_pecahan_benar'},
        {'id': '7_B', 'indicator': 'parsial', 'text': 'terbalik_pembilang_penyebut'},
        {'id': '7_C', 'indicator': 'parsial', 'text': 'belum_sederhanakan'}
    ],
    # Q8
    [
        {'id': '8_A', 'indicator': 'lemah', 'text': 'salah_strategi_bagi'},
        {'id': '8_B', 'indicator': 'tepat', 'text': 'strategi_pembagian_benar'},
        {'id': '8_C', 'indicator': 'parsial', 'text': 'salah_operasi_bagi_dua'}
    ],
    # Q9
    [
        {'id': '9_A', 'indicator': 'lemah', 'text': 'salah_strategi_perkalian'},
        {'id': '9_B', 'indicator': 'tepat', 'text': 'konteks_perkalian_benar'},
        {'id': '9_C', 'indicator': 'parsial', 'text': 'salah_hitung_konteks_perkalian'}
    ],
    # Q10
    [
        {'id': '10_A', 'indicator': 'tepat', 'text': 'konsep_rata_rata_benar'},
        {'id': '10_B', 'indicator': 'lemah', 'text': 'ambil_nilai_terkecil'},
        {'id': '10_C', 'indicator': 'lemah', 'text': 'ambil_nilai_terbesar'}
    ],
    # Q11
    [
        {'id': '11_A', 'indicator': 'lemah', 'text': 'salah_strategi_kompleks'},
        {'id': '11_B', 'indicator': 'tepat', 'text': 'strategi_kompleks_benar'},
        {'id': '11_C', 'indicator': 'parsial', 'text': 'salah_hitung_pembagian'}
    ],
    # Q12
    [
        {'id': '12_A', 'indicator': 'parsial', 'text': 'salah_strategi_bagi_konteks'},
        {'id': '12_B', 'indicator': 'tepat', 'text': 'konteks_bagi_kompleks_benar'},
        {'id': '12_C', 'indicator': 'parsial', 'text': 'salah_hitung_bagi_konteks'}
    ],
]

def simulate_student(true_level):
    """
    Simulasikan pilihan ganda seorang siswa berdasarkan target kemampuannya.
    Misal: Siswa NSI punya probabilitas tinggi memilih indikator 'lemah'.
    """
    answers = []
    for q in questions:
        # Probabilitas memilih indikator berdasarkan level anak
        if true_level == 'NSI':
            weights = [0.7 if o['indicator'] == 'lemah' else 0.2 if o['indicator'] == 'parsial' else 0.1 for o in q]
        elif true_level == 'Basic':
            weights = [0.2 if o['indicator'] == 'lemah' else 0.5 if o['indicator'] == 'parsial' else 0.3 for o in q]
        elif true_level == 'Proficient':
            weights = [0.05 if o['indicator'] == 'lemah' else 0.2 if o['indicator'] == 'parsial' else 0.75 for o in q]
        elif true_level == 'Advanced':
            weights = [0.01 if o['indicator'] == 'lemah' else 0.04 if o['indicator'] == 'parsial' else 0.95 for o in q]
        
        chosen = random.choices(q, weights=weights, k=1)[0]
        answers.append(chosen['id'])
    return answers

if __name__ == '__main__':
    levels = ['NSI', 'Basic', 'Proficient', 'Advanced']
    # Distribusi anak SLB biasanya lebih banyak di Basic/NSI
    level_weights = [0.35, 0.40, 0.15, 0.10]
    num_samples = 1500

    filename = 'numeracy_dataset.csv'
    with open(filename, mode='w', newline='') as f:
        writer = csv.writer(f)
        # Header
        headers = ['student_id'] + [f'Q{i}' for i in range(1, 13)] + ['Target_Level']
        writer.writerow(headers)

        for i in range(1, num_samples + 1):
            target = random.choices(levels, weights=level_weights, k=1)[0]
            ans = simulate_student(target)
            writer.writerow([f'STD_{i:04d}'] + ans + [target])
    
    print(f"Dataset berhasil dibuat: {filename} dengan {num_samples} baris!")
