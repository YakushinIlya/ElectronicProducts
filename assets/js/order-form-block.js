const { registerBlockType } = wp.blocks;
const { useSelect } = wp.data;

registerBlockType('electronic-products/order-form', {
    title: 'Форма оплаты',
    icon: 'cart',
    category: 'widgets',

    edit: () => {
        const postId = useSelect(
            (select) => select('core/editor').getCurrentPostId(),
            []
        );

        return wp.element.createElement(
            'p',
            {},
            `Форма оплаты для товара #${postId}`
        );
    },

    save: () => {
        return null;
    }
});
