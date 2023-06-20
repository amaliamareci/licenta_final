import elasticsearch
from elasticsearch import Elasticsearch
import numpy as np
from collections import Counter
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from scipy.cluster.hierarchy import fcluster, linkage
import matplotlib.pyplot as plt
import json

# Connect to ElasticSearch
es = Elasticsearch([{'host': 'localhost', 'port': 9200, 'scheme': 'http'}], basic_auth=('elastic', '9jc8ja9lpPK3P28ihfQ0'))
if es.ping():
    print('Connected to ElasticSearch')
else:
    print('Could not connect to ElasticSearch')

# Define the query to retrieve all documents from the "file" index
query = {
    "query": {
        "match_all": {}
    },
    "_source": ["file_name", "file_id", "file_text"],
    "size": 10000
}

# Execute the query and retrieve the documents
res = es.search(index="file", body=query)

# Extract text and filenames from documents
documents = []
filenames = []
for hit in res['hits']['hits']:
    documents.append(hit['_source']['file_text'])
    filenames.append(hit['_source']['file_name'])

# Vectorize the documents using TF-IDF
vectorizer = TfidfVectorizer(stop_words='english')
X = vectorizer.fit_transform(documents)

# Perform hierarchical clustering
Z = linkage(X.toarray(), method='ward')

# Set the threshold for clustering
threshold = 1.5

# Assign cluster labels to documents
labels = fcluster(Z, threshold, criterion='distance')

# Build the dictionary of hierarchical clusters
clusters_dict = {}

for i, filename in enumerate(filenames):
    cluster_chain = []
    cluster = labels[i]
    
    # Build the cluster chain from leaf to root
    while cluster != 0:
        cluster_chain.append(cluster)
        cluster = int(Z[cluster_chain[-1] - 1, 2])  # Go up one level in the hierarchy
    
    # Add the document to the corresponding cluster in the dictionary
    current_level = clusters_dict
    for c in cluster_chain[::-1]:
        if c not in current_level:
            current_level[int(c)] = {'filenames': [], 'keywords': []}
        current_level = current_level[int(c)]
    current_level['filenames'].append(filename)

# Define the number of top common words to display
top_n_words = 10


# Find top common words for each cluster
# Recursive function to compute keywords for clusters
def compute_keywords(cluster_data, documents, filenames):
    cluster_filenames = cluster_data['filenames']
    cluster_text = [documents[filenames.index(filename)] for filename in cluster_filenames]
    cluster_vector = vectorizer.transform(cluster_text)
    cluster_word_counts = Counter({word: cluster_vector.toarray()[:, vectorizer.vocabulary_[word]].sum() for word in vectorizer.vocabulary_})
    top_words = [word for word, count in cluster_word_counts.most_common(top_n_words)]
    cluster_data['keywords'] = top_words

    # Process nested clusters recursively
    for nested_cluster_data in cluster_data.values():
        if isinstance(nested_cluster_data, dict) and 'filenames' in nested_cluster_data:
            compute_keywords(nested_cluster_data, documents, filenames)


# Find top common words for each cluster
for cluster_data in clusters_dict.values():
    compute_keywords(cluster_data, documents, filenames)



# Convert keys to int for serialization
clusters_dict = {int(k): v for k, v in clusters_dict.items()}

# Print the resulting dictionary
print(clusters_dict)


# Save the output of the Python function to a file
with open("TRY.json", "w") as f:
    json.dump(clusters_dict, f)
