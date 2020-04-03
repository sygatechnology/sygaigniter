import React from 'react';
import faker from 'faker/locale/en_US';
import PropTypes from 'prop-types';

import { 
    Avatar,
    AvatarAddOn,
    Media
} from '../../../components';

const TrTableUsers = () => (
    <React.Fragment>
        <tr>
            <td className="align-middle">
                <Media>
                    <Media left className="align-self-center mr-3">
                        <Avatar.Image
                            size="md"
                            src="http://bs4.webkom.co/img/avatars/2.jpg"
                            addOns={[
                                <AvatarAddOn.Icon 
                                    className="fa fa-circle"
                                    color="white"
                                    key="avatar-icon-bg"
                                />
                            ]}
                        /> 
                    </Media>
                    <Media body>
                        <a className="mt-0 d-flex text-decoration-none" href="#">
                            { faker.name.firstName() } { faker.name.lastName() }
                        </a>
                        <span>
                            { faker.name.jobTitle() }
                        </span>
                    </Media>
                </Media>
            </td>
            <td className="align-middle">
                { faker.internet.email() }
            </td>
            <td className="align-middle">
                { faker.phone.phoneNumberFormat() }
            </td>
        </tr>
    </React.Fragment>
)
TrTableUsers.propTypes = {
    id: PropTypes.node
};
TrTableUsers.defaultProps = {
    id: "1"
};

export { TrTableUsers };
