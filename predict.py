import pandas as pd
from sqlalchemy import create_engine
from sklearn.linear_model import LinearRegression
import numpy as np
import sys

# 1. Koneksi ke MySQL (Sesuaikan sama data .env)
# Format: mysql+mysqlconnector://user:password@host:port/database
engine = create_engine("mysql+mysqlconnector://root:@127.0.0.1:3306/smart_irrigation")

def train_dan_prediksi():
    try:
        # 2. Tarik data dari database
        query = "SELECT eto, etc, curah_hujan, kebutuhan_air FROM irrigation_data"
        df = pd.read_sql(query, engine)

        if len(df) < 5:
            print("Prediksi Kebutuhan Air Besok: 0.0 (Data kurang bang, isi minimal 5 baris dulu)")
            return

        # 3. Menentukan Input (X) dan Output (y)
        # Model belajar: "Kalau cuaca begini (X), maka airnya segini (y)"
        X = df[['eto', 'etc', 'curah_hujan']]
        y = df['kebutuhan_air']

        # 4. Bikin Model AI (Linear Regression)
        model = LinearRegression()
        model.fit(X.values, y)

        # 5. Prediksi buat data terbaru
        # Ambil rata-rata data terakhir sebagai simulasi input besok
        last_data = X.tail(1).values
        prediction = model.predict(last_data)

        # Output yang bakal ditangkap sama Laravel
        print(f"Prediksi Kebutuhan Air Besok: {prediction[0]:.2f}")

    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    train_dan_prediksi()
