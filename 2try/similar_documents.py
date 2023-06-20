from elasticsearch import Elasticsearch, helpers
import json,sys
#connect to elastic search
es = None
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

def get_similar_documents():
    args = sys.argv

    # Check if there are any arguments passed
    if len(args) == 1:
        print("No command-line parameters provided.")
    else:
        filename= args[1]

    res = es.search(index="file", body = {
        'size' : 100,
        'query': {
            "match": {
                        "file_name": filename,
                        }
        }
        })
    doc_id=res['hits']['hits'][0]['_id']
    query = {
        "query": {
            "more_like_this": {
                "fields": ["file_text","file_name"],
                "like": [{
                    "_index": "file",
                    "_id": doc_id 
                }],
                "min_doc_freq": 5, 
                "max_query_terms": 25,
                "minimum_should_match": "60%"
            }
        },
    #     "_source": ["file_name","file_authors"] # only retrieve the file_name field from the similar documents
    }
        # Execute your Elasticsearch query
    response = es.search(index="file", body=query)
    # print(response)
    # Sort the results by the number of shared terms
    hits = sorted(response["hits"]["hits"], key=lambda x: x["_score"], reverse=True)

    # Get the top 3 results
    top_3_hits = hits[:3]

    # Print the filenames of the top 3 similar documents
    files = []
    for hit in top_3_hits:
        files.append(hit["_source"])
    return files

print(json.dumps(get_similar_documents()))