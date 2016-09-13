/*global initialData:object*/

var AlbumsView = (function() {
    var AlbumView = Mn.ItemView.extend({
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
        childView: AlbumView,
        events: {
            'click a[href].spa-link': spaClick
        }
    });
})();

var ImagesView = (function() {
    var ImageView = Mn.ItemView.extend({
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
        childView: ImageView,
        events: {
            'click a[href].spa-link': spaClick
        }
    });
})();

var PaginationView = Backbone.View.extend({
    events: {
        'click a[href]': spaClick
    }
});

var Headings = {
    Albums: Mn.LayoutView.extend({
        template: '#template-heading-albums',
        events: {
            'click a[href].spa-link': spaClick
        }
    }),
    Album: Mn.LayoutView.extend({
        template: '#template-heading-album',
        events: {
            'click a[href].spa-link': spaClick
        }
    })
};

var AppController = Marionette.Controller.extend({
    albums: function() {
        allAlbums = new AlbumsView();

        app.getRegion("heading").show(new Headings.Albums());
        app.getRegion("content").show(allAlbums);
        app.getRegion("pagination").empty();
    },
    album: function(id) {
        this.showCurrentImages(id, "/album/" + id);
    },
    albumPage: function(id, page) {
        this.showCurrentImages(id, "/album/" + id + "/page/" + page);
    },
    showCurrentImages: function(id, url) {
        var album = allAlbums.collection.get(id),
            currentImages = new ImagesView();

        currentImages.collection.fetch({
            url: url,
            beforeSend: function() {
                app.getRegion("content").$el.css("opacity", 0.7);
            },
            success: function(collection, data) {
                var albumInfo = new Headings.Album({
                        model: album
                    }),
                    pagination = new PaginationView({
                        el: data.pagination
                    });

                pagination.render();
                app.getRegion("heading").show(albumInfo);
                app.getRegion("content").show(currentImages);
                app.getRegion("pagination").show(pagination);
            },
            complete: function() {
                app.getRegion("content").$el.css("opacity", 1);
            }
        });
    }
});

var allAlbums;

var app = new Backbone.Marionette.Application({
    regions: {
        heading: "#region-heading",
        content: "#region-content",
        pagination: "#region-pagination"
    }
});

var router = new Marionette.AppRouter({
    controller: new AppController(),
    appRoutes: {
        "" : "albums",
        "album/:id" : "album",
        "album/:id/page/:page" : "albumPage"
    }
});

app.on('start', function() {
    Backbone.history.start({
        pushState: true
    });
});

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
    allAlbums = new AlbumsView();

    allAlbums.collection.reset(initialData.albums, {
        silent: true
    });

    app.start();
});
