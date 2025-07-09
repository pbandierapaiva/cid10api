from fastapi import FastAPI, HTTPException, Query
from fastapi.middleware.cors import CORSMiddleware
from contextlib import asynccontextmanager
from typing import List
import uvicorn

from pathlib import Path

cid_dict = {}

@asynccontextmanager
async def lifespan(app: FastAPI):
    BASE_DIR = Path(__file__).resolve().parent

    with open(BASE_DIR / "cid10.txt", encoding="utf-8") as f:
        for line in f:
            parts = line.strip().split(maxsplit=1)
            if len(parts) == 2:
                code, desc = parts
                cid_dict[code] = desc
    yield  # Application is running

app = FastAPI(lifespan=lifespan)
app.add_middleware(
    CORSMiddleware,
    allow_origins=[
        "https://redcap.unifesp.br",
        "https://redcap.unifesp.br:8001"
    ],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
    expose_headers=["*"]
)

# Enable CORS if needed
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

@app.get("/cid10/search")
def search_cid10(q: str = Query(..., min_length=2)) -> List[dict]:
    results = []
    q_lower = q.lower()
    for code, desc in cid_dict.items():
        print(code,desc)
        if q_lower in desc.lower() or q_lower in code.lower():
            results.append({"code": code, "display": f"{code} - {desc}"})
            if len(results) >= 10:
                break
    return results

@app.get("/cid10/code/{code}")
def get_cid10(code: str):
    if code in cid_dict:
        return {"code": code, "description": cid_dict[code]}
    else:
        raise HTTPException(status_code=404, detail="CID10 code not found")


# Uncomment to run directly
if __name__ == "__main__":
     uvicorn.run("cid10api:app", host="0.0.0.0", port=8001)
