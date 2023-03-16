Donations for Commerce
----------------------

This extension for [Commerce](https://modmore.com/commerce/) adds a donation widget that can be used to easily solicit donations for non-profits or other projects or causes. Depending on the chosen options, users can choose from suggested amounts and/or enter a custom amount.

Without this extension, it would be necessary to allow dynamic prices through the [ItemData](https://docs.modmore.com/en/Commerce/v1/Modules/Cart/ItemData.html) extension (which could then in theory be exploited to set custom pricing on any product, for example in a webshop), or it would involve a custom add-to-cart snippet.

With this extension we're making it a lot easier to fundraise with Commerce.

[Also see the documentation here](https://docs.modmore.com/en/Commerce/v1/Modules/Donations/index.html).

## Setting up the extension

Start by installing Commerce 1.3+ and the extension. It can be downloaded from the modmore.com provider (*soon) and is free to use - though a donation to support its development is of course appreciated. ;)

Go to your system settings, and configure the `commerce_donations.tax_group` and `commerce_donations.delivery_type` with the ID of the tax group and delivery type respectively that should be associated with the donation product. **Once added to the cart, a donation acts like any other product**, so it's important to set this up correctly - even if your cause is tax-exempt and no shipping is involved.

Next, go to Commerce > Configuration > Modules and enable the Donations module.

## Creating Causes/Projects

Now you will find the **Causes** menu item, located under **Products**, which is the primary point of configuration. Depending on your use case, you can create a single Cause/Project, or have different ones to raise money earmarked for different purposes.

Some things worth noting:

- The **Time Period** determines how the current progress towards your goal is calculated. In the default `total` option, all funds received for this Cause are totalled together. However, with the `monthly` or `yearly` options, only donations received in the last month/year respectively count. These latter options are useful for continuous fundraising.
- The **Reach goal by** will show a target date in the default widget to provide a sense of urgency in potential donators, but does not actually deactive or otherwise block donations after the selected date.
- The total donations in the selected time period are updated each time the cause is saved. That includes making changes to it in the dashboard, as well as when a donation is received and processed.
- You can view donations towards a cause under Actions > View donations.

## Showing the donation widget

To show the donation widget on your site, you will need to use the `commerce_donations.cause` snippet. It accepts the following properties:

- `&cause`: the ID of the cause to render. You could, for example, use a single-select listbox TV on a resource that has the following for the input option values to easily select it: ```@SELECT `name`, `id` FROM `[[+PREFIX]]commerce_donation_cause` WHERE `removed` = 0 ORDER BY `name` ASC```
- `&activeTpl`: the twig template used for rendering an active cause. This includes the donation form generated from the cause configuration. Defaults to `donations/cause/active.twig`, which you can find in `core/components/commerce_donations/templates/donations/cause/active.twig`. **Do NOT edit this file directly, it will be overwritten on upgrade. [Add a copy of it to your own theme](https://docs.modmore.com/en/Commerce/v1/Front_end_Theming.html).
- `&inactiveTpl`: the twig template used for rendering a cause that is inactive. This shows the current progress towards the goal, but does not allow new donations to be added. Defaults to `donations/cause/inactive.twig`, which you can find in `core/components/commerce_donations/templates/donations/cause/inactive.twig`. **Do NOT edit this file directly, it will be overwritten on upgrade. [Add a copy of it to your own theme](https://docs.modmore.com/en/Commerce/v1/Front_end_Theming.html).

In both the active and inactive cause templates, you have access to the cause object under the `cause` key. That includes:

- `{{ cause.id }}`
- `{{ cause.product }}`, ID of an automatically created product for this cause that donations will be assigned to for reporting purposes
- `{{ cause.name }}`, string, potentially localised if using Commerce in multilingual mode
- `{{ cause.description }}`, string, potentially localised if using Commerce in multilingual mode
- `{{ cause.cart_description }}`, string
- `{{ cause.image }}`, url
- `{{ cause.active }}`, boolean
- `{{ cause.goal }}`, integer
- `{{ cause.goal_formatted }}`, formatted based on currency
- `{{ cause.goal_period }}` - `total`, `month`, or `year`
- `{{ cause.goal_by }}`, unix timestamp
- `{{ cause.goal_by_formatted }}`, formatted based on your date settings
- `{{ cause.donated_total }}`, integer of the total donated to this cause
- `{{ cause.donated_total_formatted }}`, the above but formatted based on currency
- `{{ cause.donated_total_perc }}`, decimal number of how far along to the goal the cause is
- `{{ cause.donated_total_perc_formatted }}`, the above but formatted nicely
- `{{ cause.donated_period }}`, integer of the total amount donated to this cause in the past goal_period
- `{{ cause.donated_period_formatted }}`, the above but formatted based on currency
- `{{ cause.donated_period_perc }}`, decimal number indicating how far along to the goal the cause is for the past goal_period
- `{{ cause.donated_period_perc_formatted }}`, the above but formatted nicely
- `{{ cause.average_donation }}`, integer of the average donation to this cause - 0 if no donations made yet
- `{{ cause.average_donation_formatted }}`, the above but formatted based on currency
- `{{ cause.suggested_amounts }}`, string of comma separated values on configured suggested amounts (whole numbers)
- `{{ cause.allow_arbitrary_amounts }}`, boolean


## Listing previous donations

To list previous donations, you will use the `commerce_donations.donations` snippet. It has the following properties:

- `&cause`: the ID of the cause to render. You could, for example, use a single-select listbox TV on a resource that has the following for the input option values to easily select it: ```@SELECT `name`, `id` FROM `[[+PREFIX]]commerce_donation_cause` WHERE `removed` = 0 ORDER BY `name` ASC```
- `&tpl`: the twig template used for rendering the list of donations. Defaults to `donations/cause/donations.twig`, which you can find in `core/components/commerce_donations/templates/donations/cause/donations.twig`. **Do NOT edit this file directly, it will be overwritten on upgrade. [Add a copy of it to your own theme](https://docs.modmore.com/en/Commerce/v1/Front_end_Theming.html).

The twig template has access to all the cause properties (see above) for convenience, as well as `donations` array of which each object has the following keys (assuming a loop of `{% for donation in donations %}`:

- {{ donation.id }}, integer
- {{ donation.cause }}, integer
- {{ donation.test }}, boolean indicating if this donation was made in test mode
- {{ donation.order }}, id of the order this donation was made on
- {{ donation.item }}, id of the order item for this donation
- {{ donation.user }}, id of the user that made the donation, 0 if the user was not logged in
- {{ donation.donated_on }}, unix timestamp of when the donation was received
- {{ donation.donated_on_formatted }}, same as above but formatted
- {{ donation.currency }}, alpha3 currency code
- {{ donation.amount }}, integer amount that was donated, including taxes if applicable
- {{ donation.amount_formatted }}, same as above but formatted
- {{ donation.amount_ex_tax }}, integer amount that was donated, excluding taxes
- {{ donation.amount_ex_tax_formatted }}, same as above but formatted
- {{ donation.donor_public }}, always true - a donation that the user did not want to list will not appear in this list, only in the back-end
- {{ donation.donor_name }}, name for the donation attribution, if any. Note this is seperate from the users' billing address on the order.
- {{ donation.donor_note }}, a custom note entered by the user, if any.

The `commerce_donations.donations` snippet does **not** include the ability to create a new donation and has no different behavior between active and inactive causes.

## Styling

You can style and customise things as you'd like; there are lots of classes in the default output to target.

As an example, we used the following styles during development to make the widget and donation list look decent:

```css
.donation-box {
    position: relative;
    display: flex;
    flex-direction: column;
    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    border-radius: 4px;
    background: #fff;
    /* height: calc(100% - 2rem); */
    margin: 1rem 0;
    padding: 2rem;
    transition: all .3s ease;
}
.donation-box__image {
    float: right;
    width: 150px;
    height: 150px;
    object-fit: fit;
    border-radius: 10px;
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    border: 1px solid rgba(0,0,0,.1);
    margin-left: 1rem;
    margin-bottom: 1rem;
}
.donation-box__name {
    margin-top: 0;
    line-height: 1.0;
}
.donation-box__description {
    text-align: justify;
}
.donation-box__progress-bar {
    height: 1.5rem;
    background-color: #ccc;
    width: 100%;
    border-radius: 5px;
}
.donation-box__progress-goal {
    color: #777;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    text-align: center;
}
.donation-box__progress-bar__inner {
    background-color: green;
    height: 1.5rem;
    border-radius: 5px;
    min-width: 5%;
    max-width: 100%;
}
.donation-box__status {
    color: #777;
    font-style: italic;
    margin-bottom: 0;
}
.donation-box__amounts {
    display: flex;
    flex-wrap: wrap;
    margin-right: -1rem;
}
.donation-box__amount {
    position: relative;
    flex: 1 0 33.33%;
}
.donation-box__amount input[type=radio] {
    position: absolute;
    top: 0; left: 0; width: 0; height: 0; opacity: 0;
}
.donation-box__amount label {
    display: block;
    cursor: pointer;
    border-radius: 5px;
    color: #153256;
    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    transition: all .3s ease;
    background: #f9fafa;
    padding: 0.67em 1em;
    font-size: 1rem;
    margin-right: 1rem;
    margin-bottom: 1rem;
    font-weight: 600;
}
.donation-box__amount label span {
    padding-right: 1rem;
}
.donation-box__amount input[type=radio]:checked + label {
    border: 1px solid rgba(31,75,127,.5);
    box-shadow: 0 0 3px rgba(31,75,127,.1),0 5px 22px -6px rgba(31,75,127,.5);
    background: #d2e3ee;
}
.donation-box__amount--custom {
    flex: 1 0 67.77%;
}
.donation-box__amount--custom label {
    display: flex;
    align-items: center;
}
.donation-box__amount--custom label input {
    padding: 0.5rem 0.33rem;
    margin-left: auto;
}

.donation-box__details {

}
.donation-box__info {
    display: flex;
    width: 100%;
    margin-bottom: 1rem;
}
.donation-box__info label {
    width: 30%;
    padding-right: 1rem;
}
.donation-box__info--public {
    text-align: center;
}
.donation-box__info--public label {
    width: auto;
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #777;
    font-weight: 600;
}
.donation-box__info label span {
    font-size: 0.85rem;
    text-transform: uppercase;
    color: #777;
    font-weight: 600;
}
.donation-box__info input, .donation-box__info textarea, .donation-box__amount--custom__input  {
    flex: 1;

    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    border-radius: 4px;
    background: #fff;
    padding: 0.67em 0.5em;
    font-size: 1rem;

}
.donation-box__submit {
    padding: 1rem;
    width: 100%;
    border: 1px solid rgba(0,0,0,.1);
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    border-radius: 4px;
    background: green;
    color: white;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
}
.donation-box__submit:focus, .donation-box__submit:hover {
    background: darkgreen;
}

.donations {
    margin: 1rem 0;
    padding: 0;
    list-style-type: none;
}
.donation {
    padding: 1em;
    border: 1px solid rgba(0,0,0,.1);
    border-bottom-width: 0;
    box-shadow: 0 0 3px rgba(39,44,49,.02),0 5px 22px -6px rgba(39,44,49,.05);
    transition: all .3s ease;
    background: #f9fafa;
}
.donation:first-child {
    border-top-left-radius: 4px;
    border-top-right-radius: 4px;
}
.donation:last-child {
    border-bottom-width: 1px;
    border-bottom-left-radius: 4px;
    border-bottom-right-radius: 4px;
}
.donation p {
    margin: 0;
}
.donation__inner {

}
.donation--name {
    font-weight: 600;
    color: #333;
}
.donation--info {
    display: flex;
    align-items: center;
}
.donation--amount {
    color: #153256;
    font-weight: 600;
}
.donation--date {
    color: #777;
    flex: 1;
    padding-left: 1rem;
}
.donation--note {
    font-size: 0.9rem;
}
```

