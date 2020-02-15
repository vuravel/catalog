import {VueMasonryPlugin} from 'vue-masonry'

const Catalog = {
  	install (Vue, options = {}) {

  		Vue.use(VueMasonryPlugin)

		const files = require.context('./js/', true, /\.vue$/i)
		files.keys().map(key => Vue.component('Vl'+key.split('/').pop().split('.')[0], files(key).default))

	}
}
export default Catalog
