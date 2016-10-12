USER = '[USER]'
PASSWORD = '[PASSWORD]'

import pydelicious
pyd = pydelicious.apiNew(USER,PASSWORD)

posts = pyd.posts_all(tag='FlickrFavorite')
for post in posts['posts']:
    print post['href'], "\n"
    pyd.posts_delete(post['href'])
