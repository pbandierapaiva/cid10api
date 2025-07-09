# CID-10 Ontology API & REDCap External Module

This repository provides a local [FastAPI](https://fastapi.tiangolo.com/) API for querying ICD-10 (CID-10) codes and a REDCap external module for integrating this service into REDCap.

## 📂 Project Structure

```
cid10ontology/
├─ api/
│  ├─ cid10api.py         # FastAPI app for ICD-10 code lookup
│  ├─ cid10.txt           # ICD-10 codes and descriptions
│  └─ __init__.py         # Declares 'api' as a Python package
├─ cid10_module_v1.0.0/
│  ├─ cid10OntologyModule.php  # External module for REDCap
│  └─ config.json              # REDCap module metadata/config
├─ LICENSE
└─ README.md
```

---

## 🚀 FastAPI Application (`api/` folder)

### Available Endpoints:

- `GET /cid10/search?query=...`  
  Returns up to 10 results where the query string matches the code or description.

- `GET /cid10/code/{code}`  
  Returns the description for a specific ICD-10 code.

### How to Run Locally

1. (Optional) Create and activate a Python virtual environment:
   ```bash
   python -m venv venv
   source venv/bin/activate
   pip install fastapi uvicorn
   ```

2. Run the API:
   ```bash
   uvicorn api.cid10api:app --host 0.0.0.0 --port 8001 --reload
   ```

3. Open your browser at:  
   `http://localhost:8001/docs` to interact with the API.

---

## 🧩 REDCap External Module (`cid10_module_v1.0.0/`)

### Purpose

Allows REDCap to autocomplete form fields using descriptions from a local ICD-10 API.

### Configuration

In the REDCap external module configuration panel, set:

- **FastAPI service base URL**  
  Example: `https://redcap.unifesp.br:8001/`

### `config.json`

Defines module metadata such as:
- Name: ICD-10 Ontology
- URL validation for FastAPI service
- Minimum REDCap version: 12.5.0+

---

## 👨‍💻 Author

- **P. Bandiera-Paiva**  
  paiva@unifesp.br

---

## 📄 License

This project is licensed under the terms specified in the `LICENSE` file.

---

## 📌 Notes

- The `cid10.txt` file must contain lines with the following format:
  ```
  A00 Cholera
  A01 Typhoid and paratyphoid fevers
  ...
  ```
- The API includes CORS support to be accessible from web clients like REDCap even across domains.
