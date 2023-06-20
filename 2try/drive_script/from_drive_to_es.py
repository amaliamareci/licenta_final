import os
import sys
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

pp = pprint.PrettyPrinter(indent=2)

# The CLIENT_SECRETS_FILE variable specifies the name of a file that contains
# the OAuth 2.0 information for this application, including its client_id and
# client_secret.
CLIENT_SECRETS_FILE = 'client_secret.json'

# This access scope grants read-only access to the authenticated user's Drive
# account.
SCOPES = ['https://www.googleapis.com/auth/drive']
API_SERVICE_NAME = 'drive'
API_VERSION = 'v3'

def get_authenticated_service():
  flow = InstalledAppFlow.from_client_secrets_file(CLIENT_SECRETS_FILE, SCOPES)
  credentials = flow.run_local_server()
  return build(API_SERVICE_NAME, API_VERSION, credentials = credentials)

def list_drive_files3(service,folder_id, **kwargs):
    # Set the folder ID of the Google Drive folder you want to crawl
    # folder_id = '1SwzFI2ARNo0UewKO-TnHPenzHkMuielg'
    try:
        # Set the query parameter with the folder ID
        query = "parents='" + folder_id + "'"
        # Add the 'webViewLink' field to the 'fields' parameter
        fields = 'nextPageToken, files(id, name, mimeType, createdTime, modifiedTime, webViewLink)'
        # Call the API to list files in the folder
        results = service.files().list(
            q=query,
            fields=fields,
            **kwargs
        ).execute()

        # Print the file metadata and link for each file
        files = results.get('files', [])
        next_page_token = results.get('nextPageToken', None)
        while next_page_token:
            # Set the 'pageToken' parameter to retrieve the next set of results
            results = service.files().list(
                q=query,
                fields=fields,
                pageToken=next_page_token,
                **kwargs
            ).execute()
            # Append the files in the response to the existing list of files
            files.extend(results.get('files', []))
            next_page_token = results.get('nextPageToken', None)

        return files


    except HttpError as error:
        # Handle any API errors.
        if error.resp.status == 403:
            # The user or application does not have permission to access the resource.
            print('Access denied: %s' % error)
        else:
            # Some other error occurred.
            print('An error occurred: %s' % error)


def pdfparser(files):
   
   for file in files:
    file_id = file['id']
    file_drive_link = file["webViewLink"]
    file_name,file_authors = file['name'].split(",", 1)
    file_authors,smt=file_authors.split(".pdf", 1)
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

# Check if there are any arguments passed
  if len(args) == 1:
    print("No command-line parameters provided.")
  else:
    folder_id= args[1]
  # When running locally, disable OAuthlib's HTTPs verification. When
  # running in production *do not* leave this option enabled.
  os.environ['OAUTHLIB_INSECURE_TRANSPORT'] = '1'
  service = get_authenticated_service()
#   print(list_drive_files3(service,
#                 orderBy='modifiedByMeTime desc',
#                 pageSize=5))
  pdfparser(list_drive_files3(service,folder_id,
                orderBy='modifiedByMeTime desc',
                pageSize=5))
  print("Done")

