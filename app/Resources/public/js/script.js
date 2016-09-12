var AlbumsView = (function() {
    var AlbumView = Mn.View.extend({
        template: '#template-album'
    });

    var Album = Backbone.Model.extend({
        defaults: {
            id: null,
            name: "",
            images: []
        }
    });

    var Albums = Backbone.Collection.extend({
        model: Album,
        url: "/"
    });

    return Mn.CollectionView.extend({
        collection: new Albums(),
        childView: AlbumView
    });
})();

var ImagesView = (function() {
    var ImageView = Mn.View.extend({
        template: '#template-image'
    });

    var Image = Backbone.Model.extend({
        defaults: {
            id: null,
            original_filename: "",
            slug: ""
        }
    });

    var Images = Backbone.Collection.extend({
        model: Image,
        parse: function(response) {
            return response.data;
        }
    });

    return Mn.CollectionView.extend({
        collection: new Images(),
        childView: ImageView
    });
})();

// Application
var RootView = Mn.View.extend({
    el: '#region-application',
    template: false,
    regions: {
        heading: '#region-heading',
        main: '#region-content',
        pagination: '#region-pagination'
    }
});

var AlbumInfoView = Mn.View.extend({
    template: '#template-album-info'
});

var PaginationView = Mn.View.extend({
    events: {
        'click a': function(e) {
            spaClick(e);
        }
    }
});

var Pages = {
    Albums: Mn.View.extend({
        template: '#template-page-albums',
        regions: {
            albums: '.albums-container'
        }
    }),
    Album: Mn.View.extend({
        template: '#template-page-album',
        regions: {
            album: '.album-container',
            images: '.images-container'
        }
    })
};

var Application = Mn.Application.extend({
    region: '#viewport',
    onStart: function() {
        this.showView(new RootView());

        Backbone.history.start({
            pushState: true
        });
    }
});

var Controller = {
    showCurrentImages: function (id, url) {
        var album = allAlbums.collection.get(id),
            currentImages = new ImagesView();

        currentImages.collection.fetch({
            url: url,
            success: function(collection, data) {
                var albumPage = new Pages.Album(),
                    albumInfo = new AlbumInfoView({
                        model: album
                    }),
                    pagination = new PaginationView({
                        el: data.pagination
                    });

                app.getView().getRegion("main").show(albumPage);
                app.getView().getRegion("pagination").show(pagination);
                albumPage.getRegion("album").show(albumInfo);
                albumPage.getRegion("images").show(currentImages);
            }
        });
    }
};

var AppRouter = Mn.AppRouter.extend({
    routes : {
        "" : "albums",
        "album/:id" : "album",
        "album/:id/page/:page" : "albumPage"
    },
    albums: function() {
        var albumsPage = new Pages.Albums();

        app.getView().getRegion("main").show(albumsPage);
        albumsPage.getRegion("albums").show(allAlbums);
    },
    album: function(id) {
        Controller.showCurrentImages(id, "/album/" + id);
    },
    albumPage: function(id, page) {
        Controller.showCurrentImages(id, "/album/" + id + "/page/" + page);
    }
});

var app = new Application();
var router = new AppRouter();
var allAlbums = new AlbumsView();

function spaClick(e) {
    if (!e.altKey && !e.ctrlKey && !e.metaKey && !e.shiftKey) {
        e.preventDefault();

        var url = $(e.currentTarget).attr("href");

        router.navigate(url, {
            trigger: true
        });
    }
}

$(function() {
    app.start();

    $(document).on('click', 'a.spa-link', spaClick);
});
