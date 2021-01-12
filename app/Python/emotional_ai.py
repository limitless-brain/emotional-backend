# remember to install python3-pip

import os
import sys, getopt
import pathlib

def install_package(package):
    os.system('python3 -m pip install ' + package)


# try:
#     import tensorflow
# except:
#     install_package('tensorflow')

try:
    import keras
except:
    install_package('keras')

try:
    import pandas as pd
except:
    install_package('pandas')

import re
import json
import html

import pickle

import numpy as np
import pandas as pd

# ai
from keras.models import load_model
from keras.preprocessing.text import Tokenizer
from keras.preprocessing.sequence import pad_sequences

# current path
path = str(pathlib.Path(__file__).parent.absolute())

# text noise cleaning
def clean_text(text, space_replacer=' '):

  # filter to allow only alphabets
  text = re.sub(r'[^a-zA-Z]',' ', text)

  # remove unicode character
  text = re.sub(r'[^\x00-\x7f]+', '', text)

  # remove extra spaces
  text = re.sub(r'\s\s+', space_replacer, text)

  # convert to lowercase to maintain consistency
  text = text.lower()

  return text

# tokenizer
# loading
def load_tokenizer(path):
  with open(path, 'rb') as handle:
    return pickle.load(handle)

# labels encoding
# loading
def load_labels(path):
  with open(path, 'rb') as handle:
    return pickle.load(handle)

# loading the model
model = load_model(path + '/v.0.1.3/emotional.h5')
labels = load_labels(path + '/v.0.1.3/pickle/labels.pickle')
tokenizer = load_tokenizer(path + '/v.0.1.3/pickle/tokenizer.pickle')

# make prediction
def predict(s):

    # split into array
    array = re.split('\n+',s)
    # clean the the string
    textArray = list(map(lambda text: clean_text(text), array))

    # convert it to model encoding
    ts = tokenizer.texts_to_sequences(textArray)
    # pad the sequence to 200
    ts = pad_sequences(ts, maxlen=256)

    # predict the emotion
    prediction = model.predict(ts)
    # convert the result to array
    roundedPrediction = map(lambda x: np.round(x, 2).tolist(), prediction.tolist())
    predictionArray = list(roundedPrediction)

    # make a dictionary of emotions and predictions
    result = dict(zip(array, predictionArray))

    # overall result
    overall = [round(sum(x),2) for x in zip(*predictionArray)]

    # final result
    emotions = {
        'labels' : labels.tolist(),
        'overall' : overall,
        'emotions' : result
    }

    # return a json string
    return json.dumps(emotions)

def main(argv):
    str=''
    try:
        opts, args = getopt.getopt(argv,'hp:',['predict='])
    except getopt.GetoptError:
        print('wrong\nemotional_ai.py -p <str>')
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            print ('emotional_ai.py -p <str>')
            sys.exit()
        elif opt in ('-p','--predict'):
            str = arg
    text = html.unescape(str)
    prediction = predict(str)
    os.system('clear')
    print(prediction)

if __name__ == '__main__':
    main(sys.argv[1:])

