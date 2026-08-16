import pandas as pd
import math

df = pd.read_excel('data/CLIENTES Y PRODUCTOS MACANAO Y MATO GROSO.xlsx', sheet_name='DATA')

empresas = []
rubros = []

for idx, row in df.iterrows():
    col1 = row['Unnamed: 1']
    col2 = row['Unnamed: 2']
    col3 = row['Unnamed: 3']

    if pd.isna(col1):
        continue

    # Si col2 no es na, asumimos que es una empresa
    if not pd.isna(col2):
        empresas.append({
            'razon_social': str(col1).strip().replace("'", "\\'"),
            'rif': str(col2).strip().replace("'", "\\'"),
            'direccion': str(col3).strip().replace("'", "\\'") if not pd.isna(col3) else ''
        })
    else:
        rubros.append({
            'nombre': str(col1).strip().replace("'", "\\'")
        })

print("Empresas:")
for emp in empresas:
    print(f"            ['razon_social' => '{emp['razon_social']}', 'rif' => '{emp['rif']}', 'direccion' => '{emp['direccion']}'],")

print("\nRubros:")
for rub in rubros:
    print(f"            ['nombre' => '{rub['nombre']}'],")

