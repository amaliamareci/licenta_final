import PyPDF2
import json
import elasticsearch
from elasticsearch import Elasticsearch 
import sys



# def search_books():
#     # define the search query with full-text search and phonetic matching
#     args = sys.argv

#     # Check if there are any arguments passed
#     if len(args) == 1:
#         print("No command-line parameters provided.")
#     else:
#         search_terms= args[1:]
#         search_terms = " ".join(search_terms)
#         print(search_terms)
#         es = None
#         # es = Elasticsearch([{'host': 'localhost', 'port': 9200}])
#         es = Elasticsearch(
#     [{
#         'host': str("localhost"),
#         'port': 9200,
#         'scheme': "http"
#     }],
#     basic_auth = (str("elastic"), str("9jc8ja9lpPK3P28ihfQ0"))
#     )
#         if es.ping():
#             print('Yupiee  Connected ')
#         else:
#             print('Awww it could not connect!')
        
#         total = []
#         search_query = {
#     "query": {
#         "bool": {
#             "must": [
#                 {
#                     "match": {
#                         "file_text": {
#                             "query": search_terms
#                         }
#                     }
#                 }
#             ],
#             "should": [
#                 {
#                     "match": {
#                         "file_text": {
#                             "query": search_terms,
#                             "analyzer": "my_ngram_analyzer"
#                         }
#                     }
#                 },
#                 {
#                     "fuzzy": {
#                         "file_text": {
#                             "value": search_terms,
#                             "fuzziness": "AUTO"
#                         }
#                     }
#                 }
#             ]
#         }
#     }
# }

#         # perform the search
#         search_results = es.search(index="file", body=search_query)
#         for hit in search_results['hits']['hits']:
#             total.append(hit['_source'])
#         return total

# print(json.dumps(search_books()))

def search_books_v2():
    args = sys.argv

    if len(args) == 1:
        print("No command-line parameters provided.")
    else:
        search_terms= args[1:]
        search_terms = " ".join(search_terms)
        print(search_terms)
        es = Elasticsearch(
            [{
                'host': str("localhost"),
                'port': 9200,
                'scheme': "http"
            }],
            basic_auth = (str("elastic"), str("9jc8ja9lpPK3P28ihfQ0"))
        )
        if es.ping():
            print('Connected')
        else:
            print('Could not connect!')
        
        total = []
        search_query = {
    "query": {
        "bool": {
            "should": [
                {
                    "match": {
                        "file_text": {
                            "query": search_terms,
                            "analyzer": "my_ngram_analyzer"
                        }
                    }
                },
                {
                    "fuzzy": {
                        "file_text": {
                            "value": search_terms,
                            "fuzziness": "AUTO"
                        }
                    }
                },
                {
                    "match": {
                        "file_name": {
                            "query": search_terms,
                            "analyzer": "my_ngram_analyzer"
                        }
                    }
                },
                {
                    "fuzzy": {
                        "file_name": {
                            "value": search_terms,
                            "fuzziness": "AUTO"
                        }
                    }
                }
            ]
        }
    },
    "highlight": {
        "fields": {
            "file_text": {},
            "file_name": {}
        },
        "require_field_match": False,
        "fragment_size": 150,
        "number_of_fragments": 1,
        "pre_tags": ["<mark>"],
        "post_tags": ["</mark>"]
    },
    "_source": ["file_name"],
    "size": 2,
    "aggs": {
        "top_files": {
            "terms": {
                "field": "file_name",
                "size": 2
            }
        }
    }
}

        search_results = es.search(index="file", body=search_query)
        for hit in search_results['hits']['hits']:
            text_snippet = hit["highlight"]["file_text"][0] if "highlight" in hit else ""
            file_name = hit["_source"]["file_name"]
            total.append({
                "file_name": file_name,
                "text_snippet": text_snippet
            })
        return total

print(json.dumps(search_books_v2()))

