from fastapi import FastAPI, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
from typing import List
import uvicorn

app = FastAPI()

# Enable CORS if needed
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

cid_dict = {}

@app.on_event("startup")
def load_data():
    with open("cid10.txt", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(maxsplit=1)
            if len(parts) == 2:
                code, desc = parts
                cid_dict[code] = desc

@app.get("/cid10/search")
def search_cid10(q: str = Query(..., min_length=2)) -> List[dict]:
    results = []
    q_lower = q.lower()
    print(q)
    for code, desc in cid_dict.items():
        print(code,desc)
        if q_lower in desc.lower():
            results.append({"value": code, "label": f"{code} - {desc}"})
            if len(results) >= 10:
                break
    return results

@app.get("/cid10/{code}")
def get_cid10(code: str):
    if code in cid_dict:
        return {"code": code, "description": cid_dict[code]}
    else:
        raise HTTPException(status_code=404, detail="CID10 code not found")


# Uncomment to run directly
if __name__ == "__main__":
     uvicorn.run("cid10api:app", host="0.0.0.0", port=8001)
