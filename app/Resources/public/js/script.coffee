spaClick = (e) ->
  if !e.altKey and !e.ctrlKey and !e.metaKey and !e.shiftKey
    e.preventDefault()
    url = $(e.currentTarget).attr('href')
    router.navigate url, trigger: true
  return

# Albums collection
AlbumsView = do ->
  AlbumView = Mn.ItemView.extend
    template: '#template-album'

  Album = Backbone.Model.extend
    defaults:
      id: null,
      name: "",
      images: []

  Albums = Backbone.Collection.extend
    model: Album,
    url: "/"

  Mn.CollectionView.extend
    collection: new Albums(),
    childView: AlbumView,
    events:
      'click a[href].spa-link': spaClick

# Images collection
ImagesView = do ->
  ImageView = Mn.ItemView.extend
	  template: '#template-image'

  Image = Backbone.Model.extend
    defaults:
      id: null,
      original_filename: "",
      slug: ""

  Images = Backbone.Collection.extend
    model: Image,
    parse: (response) ->
      response.data

  Mn.CollectionView.extend
    collection: new Images,
    childView: ImageView,
    events:
      'click a[href].spa-link': spaClick

# Layout
PaginationView = Backbone.View.extend
  events:
    'click a[href]': spaClick

Headings =
  Albums: Mn.LayoutView.extend
    template: '#template-heading-albums',
    events:
      'click a[href].spa-link': spaClick
  Album: Mn.LayoutView.extend
    template: '#template-heading-album',
    events:
      'click a[href].spa-link': spaClick

# Application
AppController = Marionette.Controller.extend
  albums: ->
    allAlbums = new AlbumsView

    app.getRegion("heading").show(new Headings.Albums)
    app.getRegion("content").show(allAlbums)
    app.getRegion("pagination").empty()

  album: (id) ->
    this.showAlbumImages(id, "/album/#{id}")

  albumPage: (id, page) ->
    this.showAlbumImages(id, "/album/#{id}/page/#{page}")

  showAlbumImages: (id, url) ->
    album = allAlbums.collection.get(id)
    albumImages = new ImagesView()

    albumImages.collection.fetch
      url: url
      beforeSend: ->
        app.getRegion("content").$el.css("opacity", 0.7)

      success: (collection, data) ->
        albumInfo = new Headings.Album
          model: album

        pagination = new PaginationView
          el: data.pagination

        pagination.render()
        app.getRegion("heading").show(albumInfo)
        app.getRegion("content").show(albumImages)
        app.getRegion("pagination").show(pagination)

      complete: () ->
        app.getRegion("content").$el.css("opacity", 1)


allAlbums = null

app = new Backbone.Marionette.Application
  regions:
    heading: "#region-heading",
    content: "#region-content",
    pagination: "#region-pagination"

router = new Marionette.AppRouter
  controller: new AppController,
  appRoutes:
    "" : "albums",
    "album/:id" : "album",
    "album/:id/page/:page" : "albumPage"


app.on 'start', ->
  Backbone.history.start pushState: true
  return


$ ->
  allAlbums = new AlbumsView
  allAlbums.collection.reset initialData.albums, silent: true
  app.start()
  return
