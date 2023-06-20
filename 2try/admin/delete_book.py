import PyPDF2
import json
import elasticsearch
from elasticsearch import Elasticsearch 
import sys

def delete_file_by_name():
    args = sys.argv

    # Check if there are any arguments passed
    if len(args) == 1:
        print("No command-line parameters provided.")
    else:
        file_name= args[1]

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
    
    query = {
        'query': {
            'match': {'file_id': file_name}
        },
    }
    total = []
    res = es.search(index='file', body=query)
    for hit in res['hits']['hits']:
        total.append(hit['_source']['file_name'])
    total =  ', '.join(total)

    res = es.delete_by_query(index='file', body=query)
    if isinstance(res['deleted'], int):
        num_deleted = res['deleted']
        deleted_books = []
    else:
        num_deleted = len(res['deleted'])
        deleted_books = [doc['_source']['file_name'] for doc in res['deleted']]
    return deleted_books,num_deleted,total


print(delete_file_by_name())