from elasticsearch import Elasticsearch

# Connect to Elasticsearch
es = None
es = Elasticsearch(
   [{
      'host': str("localhost"),
      'port': 9200,
      'scheme': "http"
   }],
   basic_auth = (str("elastic"), str("9jc8ja9lpPK3P28ihfQ0"))
)

# Define your index name
index_name = 'file'

# Define your search query
search_query = {
    "_source": ["_id", "_index", "_timestamp"],
    "query": {
        "match_all": {}
    }
}

# Execute the search query
result = es.search(index=index_name, body=search_query)

# Extract and print the timestamp for each document
for hit in result['hits']['hits']:
    doc_id = hit['_id']
    index = hit['_index']
    timestamp = hit['_source']['_timestamp']
    print(f"Document ID: {doc_id}, Index: {index}, Timestamp: {timestamp}")