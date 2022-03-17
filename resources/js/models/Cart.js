export default class Cart {

    constructor() {
        this.cartKey = "healthsafe_cart";
    }

    initialize() {
        const cartData = this.getCartData();
        localStorage.setItem(this.cartKey, cartData);
    }

    getCartData() {
        return JSON.parse(localStorage.getItem(this.cartKey) || []);
    }

    setCartData(data) {
        localStorage.setItem(this.cartKey, JSON.stringify(data));
    }

    addProduct(id, amount) {
        const cart = this.getCartData();
        const index = cart.findIndex(item => item.id == id);

        if (index >= 0) {
            cart[index]["amount"] += amount;
        } else {
            cart.push({ id, amount });
        }

        this.setCartData(cart);
    }

    setProductAmount(id, amount) {
        const cart = this.getCartData();
        const index = cart.findIndex(item => item.id == id);

        if (index >= 0) {
            cart[index]["amount"] = amount;
        } else {
            cart.push({ id, amount });
        }

        this.setCartData(cart);
    }

    removeProduct(id) {
        const cart = this.getCartData();
        const index = cart.findIndex(item => item.id == id);

        if (index >= 0) {
            cart.splice(index, 1);
        }

        this.setCartData(cart);
    }

    getProductInCart(id) {
        const cart = this.getCartData();
        const index = cart.findIndex(item => item.id == id);

        return index >= 0 ? cart[index] : null;
    }

    clear() {
        this.setCartData([]);
    }

}