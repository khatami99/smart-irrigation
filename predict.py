import pandas as pd
from sqlalchemy import create_engine
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import r2_score, mean_squared_error
import numpy as np
import warnings
warnings.filterwarnings('ignore')

# ── Koneksi DB ───────────────────────────────────────────────
engine = create_engine("mysql+mysqlconnector://root:@127.0.0.1:3306/smart_irrigation")

def train_dan_prediksi():
    try:
        # ── 1. Tarik data mentah (fitur iklim asli) ──────────
        query = """
            SELECT tanggal, suhu_max, suhu_min, kelembaban,
                   kecepatan_angin, radiasi_matahari, curah_hujan,
                   kc, kebutuhan_air
            FROM irrigation_data
            ORDER BY tanggal ASC
        """
        df = pd.read_sql(query, engine, parse_dates=['tanggal'])

        if len(df) < 10:
            print("Prediksi Kebutuhan Air Besok: 0.0 (Data minimal 10 baris)")
            return

        # ── 2. Feature engineering ───────────────────────────
        # Suhu rata-rata
        df['suhu_avg'] = (df['suhu_max'] + df['suhu_min']) / 2

        # Range suhu (indikator kondisi atmosfer)
        df['suhu_range'] = df['suhu_max'] - df['suhu_min']

        # Rolling average 3 hari (tren jangka pendek)
        df['kebutuhan_roll3'] = df['kebutuhan_air'].shift(1).rolling(3).mean()
        df['hujan_roll3']     = df['curah_hujan'].shift(1).rolling(3).mean()

        # Lag 1 hari — kebutuhan air kemarin sebagai fitur
        df['kebutuhan_lag1'] = df['kebutuhan_air'].shift(1)
        df['hujan_lag1']     = df['curah_hujan'].shift(1)

        # Bulan sebagai fitur musiman
        df['bulan'] = df['tanggal'].dt.month

        # Target: kebutuhan air BESOK (shift -1)
        df['target'] = df['kebutuhan_air'].shift(-1)

        # Hapus baris dengan NaN
        df = df.dropna()

        if len(df) < 5:
            print("Prediksi Kebutuhan Air Besok: 0.0 (Data bersih kurang dari 5)")
            return

        # ── 3. Fitur yang dipakai ────────────────────────────
        feature_cols = [
            'suhu_avg', 'suhu_range', 'kelembaban',
            'kecepatan_angin', 'radiasi_matahari',
            'curah_hujan', 'kc', 'bulan',
            'kebutuhan_lag1', 'kebutuhan_roll3',
            'hujan_lag1', 'hujan_roll3',
        ]

        X = df[feature_cols].values
        y = df['target'].values

        # ── 4. Split train/test (80/20) ──────────────────────
        split = int(len(X) * 0.8)
        X_train, X_test = X[:split], X[split:]
        y_train, y_test = y[:split], y[split:]

        # ── 5. Scaling ───────────────────────────────────────
        scaler = StandardScaler()
        X_train_sc = scaler.fit_transform(X_train)
        X_test_sc  = scaler.transform(X_test)

        # ── 6. Train model ───────────────────────────────────
        model = LinearRegression()
        model.fit(X_train_sc, y_train)

        # ── 7. Evaluasi akurasi ──────────────────────────────
        if len(X_test) > 0:
            y_pred_test = model.predict(X_test_sc)
            r2   = r2_score(y_test, y_pred_test)
            rmse = np.sqrt(mean_squared_error(y_test, y_pred_test))
        else:
            r2, rmse = 0.0, 0.0

        # ── 8. Prediksi besok pakai data terbaru ─────────────
        last = df.iloc[-1]
        input_besok = np.array([[
            last['suhu_avg'],
            last['suhu_range'],
            last['kelembaban'],
            last['kecepatan_angin'],
            last['radiasi_matahari'],
            last['curah_hujan'],
            last['kc'],
            last['bulan'],
            last['kebutuhan_air'],   # kebutuhan hari ini = lag1 untuk besok
            last['kebutuhan_roll3'],
            last['curah_hujan'],     # hujan hari ini = lag1 untuk besok
            last['hujan_roll3'],
        ]])

        input_besok_sc = scaler.transform(input_besok)
        prediksi = model.predict(input_besok_sc)[0]

        # Pastikan tidak negatif
        prediksi = max(0.0, prediksi)

        # ── 9. Output ────────────────────────────────────────
        print(f"Prediksi Kebutuhan Air Besok: {prediksi:.2f}")
        print(f"Akurasi Model R2: {r2:.3f}")
        print(f"RMSE: {rmse:.3f}")

    except Exception as e:
        print(f"Error: {e}")
        print("Prediksi Kebutuhan Air Besok: 0.0")

if __name__ == "__main__":
    train_dan_prediksi()
