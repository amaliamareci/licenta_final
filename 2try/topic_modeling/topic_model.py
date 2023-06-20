import gensim
import string
from gensim.models import LdaModel
from gensim.corpora import Dictionary
from elasticsearch import Elasticsearch 
import pyLDAvis
import pyLDAvis.gensim
import gensim
from nltk.corpus import stopwords

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
    "_source":  ["file_name", "file_id", "file_text"],
    "size": 10000
}
res = es.search(index="file", body=query)
data = []
for hit in res['hits']['hits']:
    data.append(hit['_source']['file_text'])

# Remove stop words
import nltk
nltk.download('stopwords')
nltk.download('punkt')

stop_words = set(stopwords.words('english'))

# Add numbers and punctuation to the stop words list
stop_words.update(set(string.punctuation))
stop_words.update(set(['0', '1', '2', '3', '4', '5', '6', '7', '8', '9']))

data = [[word for word in doc.split() if word.lower() not in stop_words] for doc in data]

NUM_TOPICS = 10
# Convert CountVectorizer to Gensim dictionary
# Convert your data into a list of tokenized documents
tokenized_data = data

# Convert CountVectorizer to Gensim dictionary
id2word = Dictionary(tokenized_data)

# Build a bag-of-words corpus
corpus = [id2word.doc2bow(doc) for doc in tokenized_data]

# Convert the scikit-learn LDA model to Gensim LdaModel
gensim_lda_model = LdaModel(
    corpus=corpus,
    id2word=id2word,
    num_topics=NUM_TOPICS,
    passes=10
)

# Generate the visualization
panel = pyLDAvis.gensim.prepare(gensim_lda_model, corpus, id2word, mds='tsne')

# Save the visualization to an HTML file
pyLDAvis.save_html(panel, 'lda_visualization.html')
