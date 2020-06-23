import React from 'react';
import PropTypes from 'prop-types';
import classNames from 'classnames';
import _ from 'lodash';
import { NavLink } from 'reactstrap';

import { Consumer } from './context';

const UncontrolledTabsNavLink = (props) => (
    <Consumer>
    {
        (value) => (
            <NavLink
                { ..._.omit(props, ['tabId']) }
                onClick={ () => { value.setActiveTabId(props.tabId); props.onClick } }
                className={ classNames({ active: props.tabId === value.activeTabId }) }
            />
        )
    }
    </Consumer>
);
UncontrolledTabsNavLink.propTypes = {
    tabId: PropTypes.string.isRequired
};

export { UncontrolledTabsNavLink };

