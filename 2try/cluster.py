import elasticsearch
from elasticsearch import Elasticsearch 
import numpy as np
from collections import Counter
import re
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import AgglomerativeClustering
from scipy.cluster.hierarchy import dendrogram, linkage
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

# Perform agglomerative clustering with a distance threshold of 0.5
model = AgglomerativeClustering(distance_threshold=1.5, n_clusters=None)
model.fit(X.toarray())


# Build a dictionary of cluster assignments for each file
clusters = {}
for i in range(len(filenames)):
    cluster_label = model.labels_[i]
    if cluster_label not in clusters:
        clusters[cluster_label] = {'filenames': [], 'keywords': []}
    clusters[cluster_label]['filenames'].append(filenames[i])

# Define the number of top common words to display
top_n_words = 10

# Find top common words for each cluster
for i, cluster_data in clusters.items():
    print("Cluster " + str(i) + ":")
    cluster_filenames = cluster_data['filenames']
    cluster_text = [documents[filenames.index(filename)] for filename in cluster_filenames]
    cluster_vector = vectorizer.transform(cluster_text)
    cluster_word_counts = Counter({word: cluster_vector.toarray()[:,vectorizer.vocabulary_[word]].sum() for word in vectorizer.vocabulary_})
    top_words = [word for word, count in cluster_word_counts.most_common(top_n_words)]
    cluster_data['keywords'] = top_words

# Create the dictionary with desired format
clusters_dict = {}
for i, cluster_data in clusters.items():
    cluster_label = "Cluster" + str(i)
    filenames = cluster_data['filenames']
    keywords = cluster_data['keywords']
    clusters_dict[cluster_label] = (filenames, keywords)

# Print the resulting dictionary
print(clusters_dict)

# Save the output of the Python function to a file
with open("cluster_output.json", "w") as f:
    json.dump(clusters_dict, f)