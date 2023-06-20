import os
import pprint
import io
import PyPDF2
import google.oauth2.credentials
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError
from google_auth_oauthlib.flow import InstalledAppFlow
import pdfrw
from pdfrw import PdfReader
from elasticsearch import Elasticsearch
import sys

pp = pprint.PrettyPrinter(indent=2)

import os
current_dir = os.path.dirname(__file__)
client_secrets_file = os.path.join(current_dir, 'client_secret.json')
# print("!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!",client_secrets_file)
# The CLIENT_SECRETS_FILE variable specifies the name of a file that contains
# the OAuth 2.0 information for this application, including its client_id and
# client_secret.
CLIENT_SECRETS_FILE = client_secrets_file

# This access scope grants read-only access to the authenticated user's Drive
# account.
SCOPES = ['https://www.googleapis.com/auth/drive']
API_SERVICE_NAME = 'drive'
API_VERSION = 'v3'

def get_authenticated_service():
  flow = InstalledAppFlow.from_client_secrets_file(CLIENT_SECRETS_FILE, SCOPES)
  credentials = flow.run_local_server()
  return build(API_SERVICE_NAME, API_VERSION, credentials = credentials)

def drive_file(service,file_id):
    # Set the file ID of the Google Drive folder you want to crawl
    #file_id = '1qdh0djxDlyqoKlqqdf6rQC-tSuv-OEO-'
    file = service.files().get(fileId=file_id, fields='id,name,mimeType,createdTime,modifiedTime,webViewLink').execute()
    return file


def pdfparser(file):
    file_id = file['id']
    file_drive_link = file["webViewLink"]
    if "," in file['name']:
        file_name,file_authors = file['name'].split(",", 1)
        file_authors,smt=file_authors.split(".pdf", 1)
    else:
        file_name = file['name'].split(".pdf", 1)
        file_name =''.join(file_name)
        file_authors = "Author unknown"
    print(file_name)
    file_content = service.files().get_media(fileId=file_id).execute()
    file_io = io.BytesIO(file_content)
    
    pdf = PyPDF2.PdfReader(file_io)
    info = pdf.metadata
    text = ''
    for page in pdf.pages:
        text += page.extract_text()
    # print(file["webViewLink"])
    # print(file_authors,file_id)
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

    query = {
    'query': {
        'match': {'file_id': file['id']}
    }
}
    
    res = es.delete_by_query(index='file', body=query)

    # # Add data to the index
    doc = {
            "file_name": file_name,
            "file_authors": file_authors,
            "file_id": file_id,
            "file_text": text,
            "file_drive_link": file_drive_link
    }
    res = es.search(index="file", body={
    'size': 100,
    'query': {
        "match": {
            "file_id": file_id,
        }
    }
})
    if res["hits"]["total"]["value"] == 0:
        es.index(index="file", body=doc)
    else:
       print("File is already in es")
    

   
if __name__ == '__main__':
  args = sys.argv
  if len(args) == 1:
    print("No command-line parameters provided.")
  else:
    file_id= args[1]
  # When running locally, disable OAuthlib's HTTPs verification. When
  # running in production *do not* leave this option enabled.
  os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'
  service = get_authenticated_service()
#   print(drive_file(service,file_id))
  pdfparser(drive_file(service,file_id))
  print("Done")


