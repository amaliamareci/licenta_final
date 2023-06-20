import PyPDF2
import json
import elasticsearch
from elasticsearch import Elasticsearch 
import sys

# Get the command-line arguments
def get_book():
    args = sys.argv

    # Check if there are any arguments passed
    if len(args) == 1:
        print("No command-line parameters provided.")
    else:
        book_title= args[1]

        es = None
        # es = Elasticsearch([{'host': 'localhost', 'port': 9200}])
        es = Elasticsearch(
    [{
        'host': str("localhost"),
        'port': 9200,
        'scheme': "http"
    }],
    basic_auth = (str("elastic"), str("9jc8ja9lpPK3P28ihfQ0"))
    )
        if es.ping():
            print('Yupiee  Connected ')
        else:
            print('Awww it could not connect!')
        
        total = []
        res = es.search(index="file", body = {
        'size' : 100,
        'query': {
            "match": {
                        "file_name": book_title,
                     }
        }
        })
        for hit in res['hits']['hits']:
            total.append(hit['_source'])
        return total
print(json.dumps(get_book()))